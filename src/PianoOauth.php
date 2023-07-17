<?php

namespace Progresivjose\PianoOauth;

use Progresivjose\PianoOauth\Actions\CheckAuthentication;
use Progresivjose\PianoOauth\Actions\GetAccessTokens;
use Progresivjose\PianoOauth\Actions\RedirectToLoginForm;
use Progresivjose\PianoOauth\Adapters\GuzzleAdapter;
use Progresivjose\PianoOauth\Entities\User;
use Progresivjose\PianoOauth\Libraries\HttpClient;
use Progresivjose\PianoOauth\Libraries\Redirect;
use Progresivjose\PianoOauth\Runners\Runner;

final class PianoOauth
{
    private Runner $runner;

    public function __construct(
        private String $aid,
        private String $apiToken,
        private String $oauthClientSecret,
        private String $baseUrl = 'https://sandbox.tinypass.com'
    ) {
        $guzzleAdapter = new GuzzleAdapter();
        $httpClient = new HttpClient($baseUrl, $guzzleAdapter);
        $this->runner = new Runner(
            new RedirectToLoginForm(new Redirect()),
            new GetAccessTokens($this->aid, $this->oauthClientSecret, $httpClient),
            new CheckAuthentication($httpClient)
        );

        $this->runner->setAid($this->aid);
    }

    public function preAuth(String $returnUrl, String $redirectUrl = 'https://sandbox.piano.io/id/', String $source = ''): void
    {
        $this->runner->preAuth($returnUrl, $redirectUrl, $source);
    }

    public function postAuth(String $code, String $redirectUrl): ?User
    {
        return $this->runner->postAuth($code, $redirectUrl);
    }
}
