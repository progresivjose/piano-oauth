<?php

namespace Progresivjose\PianoOauth\Entities;

final class AccessToken
{
    public function __construct(private String $accessToken, private String $refreshToken)
    {
    }

    public function getAccessToken(): String
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): String
    {
        return $this->refreshToken;
    }
}
