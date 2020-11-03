<?php

namespace Log\SDK;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use OAuth2ClientCredentials\OAuthClient;

class LogClient
{
    /**
     * @var OAuthClient
     */
    private $oauthClient;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->oauthClient = new OAuthClient(
            config('log.oauth.url'),
            config('log.oauth.client_id'),
            config('log.oauth.client_secret')
        );
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param callable $handler
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function request($handler)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->oauthClient->getAccessToken(),
        ])
            ->withoutVerifying();

        $response = $handler($request);

        if ($response->status() == 401) {
            $this->oauthClient->getAccessToken(true);
        }

        return $response;
    }

    /**
     * @param string $route
     * @return string
     */
    private function getUrl($route)
    {
        return $this->apiUrl . '/api/client/v1' . $route;
    }

    /**
     * @param string $type
     * @param string $version
     * @param array|null $data
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function log($type, $version, $data = null)
    {
        if (config('log.dry_run')) {
            return true;
        }

        $params = [
            'type' => $type,
            'version' => $version,
            'data' => is_array($data) ? Arr::flatten($data) : null,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/logs'), $params);
        })
            ->successful();
    }
}