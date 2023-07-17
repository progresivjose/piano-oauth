<?php

namespace Progresivjose\PianoOauth\Actions;

use Exception;
use Progresivjose\PianoOauth\Entities\Result;
use Progresivjose\PianoOauth\Libraries\Redirect;

class RedirectToLoginForm implements Action
{
    private String $redirectUrl;
    private String $returnUrl;
    private String $clientId;
    private String $source;

    public function __construct(private Redirect $redirect)
    {
    }

    public function setRedirectUrl(String $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function setReturnUrl(String $returnUrl): self
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    public function setClientId(String $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function setSource(String $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function perform(): ?Result
    {
        $this->guard();

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => "{$this->returnUrl}",
            'disable_sign_up' => 'false',
        ];

        $url = $this->redirectUrl . '?' . http_build_query($params);

        $this->redirect->to($url);

        return null;
    }

    public function guard()
    {
        if (!isset($this->redirectUrl)) {
            throw new Exception("Redirect URL Missing");
        }

        if (!isset($this->returnUrl)) {
            throw new Exception("Return URL Missing");
        }

        if (!isset($this->clientId)) {
            throw new Exception("Client ID Missing");
        }

        if (!isset($this->source)) {
            throw new Exception("Source Missing");
        }
    }
}
