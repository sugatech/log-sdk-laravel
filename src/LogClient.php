<?php

namespace Log\SDK;

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
     * @param string $version
     * @param string $data
     * @return object
     */
    public function createLog($type, $version, $data)
    {
        return $this->request()
            ->asJson()
            ->post(
                $this->getUrl('/logs'),
                [
                    'type' => $type,
                    'version' => $version,
                    'data' => $data,
                ]
            )
            ->json();
    }
}