<?php

namespace Progresivjose\PianoOauth\Adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Progresivjose\PianoOauth\Enums\HttpStatusCode;
use Progresivjose\PianoOauth\Responses\Response;

class GuzzleAdapter implements HttpClientAdapter
{
    private Client $httpClient;

    public function __construct(?Client $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new Client();
    }

    public function get(string $url, array $options = []): Response
    {
        return $this->performRequest('GET', $url, $options);
    }

    public function post(string $url, array $options = []): Response
    {
        return $this->performRequest('POST', $url, $options);
    }

    private function performRequest(String $method, String $url, array $options = []): Response
    {
        try {
            $guzzleResponse = $this->httpClient->request($method, $url, $options);

            $body = json_decode($guzzleResponse->getBody());
            $statusCode = isset($body->code) ? $body->code : $guzzleResponse->getStatusCode();

            return new Response(
                status: $this->getStatusCode($statusCode),
                content: json_decode($guzzleResponse->getBody())
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();

            return new Response(
                status: $this->getStatusCode($response->getStatusCode()),
                content: $response->getBody()
            );
        }
    }

    private function getStatusCode(int $statusCode): HttpStatusCode
    {
        return match($statusCode) {
            0 => HttpStatusCode::OK,
            200 => HttpStatusCode::OK,
            400 => HttpStatusCode::BAD_REQUEST,
            403 => HttpStatusCode::NOT_AUTHORIZED,
            404 => HttpStatusCode::NOT_FOUND,
            500 => HttpStatusCode::INTERNAL_SERVER_ERROR
        };

    }
}
