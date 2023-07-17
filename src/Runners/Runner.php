<?php

namespace Progresivjose\PianoOauth\Runners;

use Progresivjose\PianoOauth\Actions\CheckAuthentication;
use Progresivjose\PianoOauth\Actions\GetAccessTokens;
use Progresivjose\PianoOauth\Actions\RedirectToLoginForm;
use Progresivjose\PianoOauth\Entities\AccessToken;
use Progresivjose\PianoOauth\Entities\User;

class Runner
{
    private String $aid;

    public function __construct(
        private RedirectToLoginForm $redirectToLoginForm,
        private GetAccessTokens $getAccessTokens,
        private CheckAuthentication $checkAuthentication,
    ) {
    }

    public function setAid(String $aid): self
    {
        $this->aid = $aid;

        return $this;
    }

    public function preAuth(String $redirectUrl, String $returnUrl, String $source = ""): void
    {
        $this->redirectToLoginForm
            ->setRedirectUrl($redirectUrl)
            ->setReturnUrl($returnUrl)
            ->setClientId($this->aid)
            ->setSource($source)
            ->perform();
    }

    public function postAuth(String $code, String $redirectUrl): ?User
    {
        $result = $this->getAccessTokens
            ->setCode($code)
            ->setRedirectUrl($redirectUrl)
            ->perform();

        if ($result->getType() == AccessToken::class) {
            $accessToken = $result->get();

            $result = $this->checkAuthentication
                ->setAccessToken($accessToken->getAccessToken())
                ->setRefreshToken($accessToken->getRefreshToken())
                ->perform();

            if ($result->getType() == User::class) {
                return $result->get();
            }
        }
        return null;
    }
}
