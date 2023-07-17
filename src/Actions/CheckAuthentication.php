<?php

namespace Progresivjose\PianoOauth\Actions;

use Exception;
use Progresivjose\PianoOauth\Entities\Result;
use Progresivjose\PianoOauth\Entities\User;
use Progresivjose\PianoOauth\Enums\HttpStatusCode;
use Progresivjose\PianoOauth\Libraries\HttpClient;
use Progresivjose\PianoOauth\Responses\Response;

class CheckAuthentication implements Action
{
    private String $accessToken;
    private String $refreshToken;

    public const USER_GET_ENDPOINT = '/api/v3/user/get';

    public function __construct(private HttpClient $httpClient)
    {
    }

    public function setAccessToken(String $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function setRefreshToken(String $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function perform(): ?Result
    {
        $this->guard();

        $response = $this->httpClient->post(self::USER_GET_ENDPOINT, [
            'headers' => [
                'authorization' => "Bearer {$this->accessToken}"
            ]
        ]);

        if ($response->getStatus() == HttpStatusCode::OK) {
            $userData = $response->getContent();

            return new Result(
                content: new User(
                    $userData->user->uid,
                    $userData->user->first_name,
                    $userData->user->last_name,
                    $userData->user->personal_name,
                    $userData->user->email
                ),
                type: User::class
            );
        }

        return new Result($response, Response::class);
    }

    private function guard()
    {
        if (!isset($this->accessToken)) {
            throw new Exception("Missing Access Token");
        }

        if (!isset($this->refreshToken)) {
            throw new Exception("Missing Refresh Token");
        }
    }
}
