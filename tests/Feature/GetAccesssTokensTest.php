<?php

use Progresivjose\PianoOauth\Actions\GetAccessTokens;
use Progresivjose\PianoOauth\Entities\AccessToken;
use Progresivjose\PianoOauth\Enums\HttpStatusCode;
use Progresivjose\PianoOauth\Libraries\HttpClient;
use Progresivjose\PianoOauth\Responses\Response;

beforeEach(function () {
    $this->mockHttpClient = Mockery::mock(HttpClient::class);

    $this->getAccessToken = new GetAccessTokens('test-aid', 'test-secret', $this->mockHttpClient);
});

it("should require the code", function () {
    $this->getAccessToken->perform();
})->throws(Exception::class, "Code is Missing");

it("should require the redirectUrl", function () {
    $this->getAccessToken->setCode('test-code')->perform();
})->throws(Exception::class, "Redirect URL is Missing");

test("should return 500 error if there was a problem when accessing the access token", function () {
    $code = 'test-code';
    $body = [
        'client_id' => 'test-aid',
        'client_secret' => 'test-secret',
        'redirect_uri' => 'redirect-uri',
        'grant_type' => 'authorization_code',
        'code' => $code,
    ];

    $this->mockHttpClient->shouldReceive('post')
        ->once()
        ->with(GetAccessTokens::ACCESS_TOKEN_ENDPOINT, [
            'form_params' => $body
        ])
        ->andReturn(new Response(HttpStatusCode::INTERNAL_SERVER_ERROR, "Could not get the access token"));

    $result = $this->getAccessToken->setCode($code)->setRedirectUrl('redirect-uri')->perform();
    $response = $result->get();

    expect($response->getStatus())->toBe(HttpStatusCode::INTERNAL_SERVER_ERROR);
    expect($response->getContent())->toBe("Could not get the access token");
});

it("should return the access tokens", function () {
    $code = 'test-code';
    $tokens = json_decode(json_encode(['access_token' => 'access-test', 'refresh_token' => 'refresh-test']));
    $body = [
        'client_id' => 'test-aid',
        'client_secret' => 'test-secret',
        'redirect_uri' => 'redirect-uri',
        'grant_type' => 'authorization_code',
        'code' => $code,
    ];

    $this->mockHttpClient->shouldReceive('post')
        ->once()
        ->with(GetAccessTokens::ACCESS_TOKEN_ENDPOINT, [
            'form_params' => $body
        ])
        ->andReturn(new Response(HttpStatusCode::OK, $tokens, 'object'));

    $result = $this->getAccessToken->setCode($code)->setRedirectUrl('redirect-uri')->perform();
    $accessToken = $result->get();

    expect($result->getType())->toBe(AccessToken::class);
    expect($accessToken->getAccessToken())->toBe('access-test');
    expect($accessToken->getRefreshToken())->toBe('refresh-test');
});
