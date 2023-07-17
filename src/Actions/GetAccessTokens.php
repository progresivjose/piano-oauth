<?php

namespace Progresivjose\PianoOauth\Actions;

use Exception;
use Progresivjose\PianoOauth\Entities\AccessToken;
use Progresivjose\PianoOauth\Entities\Result;
use Progresivjose\PianoOauth\Enums\HttpStatusCode;
use Progresivjose\PianoOauth\Libraries\HttpClient;
use Progresivjose\PianoOauth\Responses\Response;

class GetAccessTokens implements Action
{
    private String $code;

    private String $redirectUrl;

    public const ACCESS_TOKEN_ENDPOINT = '/id/api/v1/identity/vxauth/token';

    public function __construct(private String $aid, private String $clientSecret, private HttpClient $httpClient)
    {
    }

    public function setCode(String $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setRedirectUrl(String $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function perform(): ?Result
    {
        if (!isset($this->code)) {
            throw new Exception("Code is Missing");
        }

        if (!isset($this->redirectUrl)) {
            throw new Exception("Redirect URL is Missing");
        }

        $response = $this->httpClient->post(self::ACCESS_TOKEN_ENDPOINT, [
            'form_params' => [
                'client_id' => $this->aid,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUrl,
                'grant_type' => 'authorization_code',
                'code' => $this->code,
            ]
        ]);

        if ($response->getStatus() == HttpStatusCode::OK) {
            return new Result(
                content: new AccessToken(
                    accessToken: $response->getContent()->access_token,
                    refreshToken: $response->getContent()->refresh_token
                ),
                type: AccessToken::class
            );
        }

        return new Result($response, Response::class);
    }
}
