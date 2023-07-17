<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Progresivjose\PianoOauth\Adapters\GuzzleAdapter;
use Progresivjose\PianoOauth\Enums\HttpStatusCode;

beforeEach(function () {
    $this->mockGuzzle = Mockery::mock(Client::class);

    $this->adapter = new GuzzleAdapter($this->mockGuzzle);
});

it("should return a http status code 200 if everything is OK", function () {
    $this->mockGuzzle->shouldReceive('request')
        ->once()
        ->with('GET', 'https://example.test', [])
        ->andReturn(new Response(200, [], '{"result": "OK"}'));

    $response = $this->adapter->get("https://example.test");

    expect($response->getStatus())->toBe(HttpStatusCode::OK);
    expect(get_class($response->getContent()))->toBe('stdClass');
    expect($response->getContent()->result)->toBe('OK');
});

it("should return http status code 404 if the client throws exception with 404", function () {
    $this->mockGuzzle->shouldReceive('request')
        ->once()
        ->with('GET', 'https://example.test', [])
        ->andThrows(new ClientException("Not Found 404", new Request("GET", "http://example.test"), new Response(404, [], "Not Found")));


    $response = $this->adapter->get("https://example.test");

    expect($response->getStatus())->toBe(HttpStatusCode::NOT_FOUND);
});
