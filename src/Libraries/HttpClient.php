<?php

namespace Progresivjose\PianoOauth\Libraries;

use Progresivjose\PianoOauth\Adapters\HttpClientAdapter;
use Progresivjose\PianoOauth\Responses\Response;

class HttpClient
{
    private static HttpClient $instance;

    public function __construct(private String $baseUrl, private HttpClientAdapter $clientAdapter)
    {
    }

    public static function getInstance(String $baseUrl = '', HttpClientAdapter $httpClientAdapter): HttpClient
    {
        if (!isset(self::$instance)) {
            self::$instance = new HttpClient($baseUrl, $httpClientAdapter);
        }
        return self::$instance;
    }
    public function get(String $url, array $options = []): Response
    {
        return $this->clientAdapter->get($this->getFullUrl($url), $options);
    }

    public function post(String $url, array $options = []): Response
    {
        return $this->clientAdapter->post($this->getFullUrl($url), $options);
    }

    private function getFullUrl(String $url)
    {
        return "{$this->baseUrl}{$url}";
    }
}
