<?php

namespace Log\SDK;

use Illuminate\Support\Arr;
use Zttp\PendingZttpRequest;
use Zttp\Zttp;

class LogClient
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @param string $apiUrl
     * @param string $accessToken
     */
    public function __construct($apiUrl, $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return PendingZttpRequest
     */
    private function request()
    {
        return Zttp::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])
            ->withoutVerifying();
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
     * @param array $entries
     * @return bool
     */
    public function log($type, $entries)
    {
        if (config('log.dry_run')) {
            return true;
        }

        $keys = array_column($entries, 'key');
        $data = array_column($entries, 'value');

        $version = sha1(implode(',', $keys));

        return $this->request()
            ->asJson()
            ->post(
                $this->getUrl('/logs'),
                [
                    'type' => $type,
                    'version' => $version,
                    'data' => is_array($data) ? Arr::flatten($data) : null,
                ]
            )
            ->isSuccess();
    }
}