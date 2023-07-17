<?php

use Progresivjose\PianoOauth\Adapters\HttpClientAdapter;
use Progresivjose\PianoOauth\Libraries\HttpClient;

beforeEach(function () {
    $this->mockAdapter = Mockery::mock(HttpClientAdapter::class);
});

it("should return the instance for singleton", function () {
    $instance = HttpClient::getInstance('http://example.test', $this->mockAdapter);

    expect(get_class($instance))->toBe(HttpClient::class);

    expect(HttpClient::getInstance('http://example.test', $this->mockAdapter))->toBe($instance);
});

it("should call the adapter with the base url", function () {
    $this->mockAdapter->shouldReceive('get')
        ->once()
        ->with('http://example.test/john-doe', []);

    $httpClient = new HttpClient('http://example.test', $this->mockAdapter);

    $httpClient->get('/john-doe');
});

it("should call the adapter with the base url for post request", function () {
    $this->mockAdapter->shouldReceive('post')
        ->once()
        ->with('http://example.test/john-doe', []);

    $httpClient = new HttpClient('http://example.test', $this->mockAdapter);

    $httpClient->post('/john-doe');
});
