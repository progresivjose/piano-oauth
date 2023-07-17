<?php

use Progresivjose\PianoOauth\Actions\CheckAuthentication;
use Progresivjose\PianoOauth\Entities\User;
use Progresivjose\PianoOauth\Enums\HttpStatusCode;
use Progresivjose\PianoOauth\Libraries\HttpClient;
use Progresivjose\PianoOauth\Responses\Response;

beforeEach(function () {
    $this->mockHttpClient = Mockery::mock(HttpClient::class);

    $this->action = new CheckAuthentication($this->mockHttpClient);
});

it("should require the access_token", function () {
    $this->action->perform();
})->throws(Exception::class, "Missing Access Token");

it("should require the refresh_token", function () {
    $this->action->setAccessToken('test')->perform();
})->throws(Exception::class, "Missing Refresh Token");

it("should return response with 500 error if there is a problem on request", function () {
    $this->mockHttpClient->shouldReceive('post')
        ->once()
        ->with(CheckAuthentication::USER_GET_ENDPOINT, [
            'headers' => [
                'authorization' => 'Bearer test-access-token'
            ]
        ])
        ->andReturn(new Response(HttpStatusCode::INTERNAL_SERVER_ERROR, "There was a problem with the API Response"));

    $response = $this->action
        ->setAccessToken('test-access-token')
        ->setRefreshToken('test')
        ->perform()
        ->get();

    expect($response->getStatus())->toBe(HttpStatusCode::INTERNAL_SERVER_ERROR);
    expect($response->getContent())->toBe("There was a problem with the API Response");
});

it("should return the user if the response is OK", function () {
    $data = [
        "code" => 0,
        "ts" => 1666039217,
        "user" => [
            "first_name" => "John",
            "last_name" => "Doe",
            "personal_name" => "John Doe",
            "email" => "john@doe.co",
            "uid" => "uid-test",
            "image" => null
        ]
    ];

    $this->mockHttpClient->shouldReceive('post')
        ->once()
        ->with(CheckAuthentication::USER_GET_ENDPOINT, [
            'headers' => [
                'authorization' => 'Bearer test-access-token'
            ]
        ])
        ->andReturn(new Response(HttpStatusCode::OK, json_decode(json_encode($data)), 'object'));

    $response = $this->action
        ->setAccessToken('test-access-token')
        ->setRefreshToken('test')
        ->perform();

    expect($response->getType())->toBe(User::class);
    expect($response->get()->getUID())->toBe("uid-test");
    expect($response->get()->getEmail())->toBe("john@doe.co");
});
