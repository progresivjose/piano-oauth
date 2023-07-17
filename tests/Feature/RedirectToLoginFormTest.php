<?php

use Progresivjose\PianoOauth\Actions\RedirectToLoginForm;
use Progresivjose\PianoOauth\Libraries\Redirect;

beforeEach(function () {
    $this->mockRedirect = Mockery::mock(Redirect::class);

    $this->action = new RedirectToLoginForm($this->mockRedirect);
});

it("should require the redirect url", function () {
    $this->action->perform();
})->throws(Exception::class, "Redirect URL Missing");

it("should require the return url", function () {
    $this->action
        ->setRedirectUrl("http://example.test")
        ->perform();
})->throws(Exception::class, "Return URL Missing");

it("should require the client id", function () {
    $this->action
        ->setRedirectUrl("http://example.text")
        ->setReturnUrl("http://mysite.test")
        ->perform();
})->throws(Exception::class, "Client ID Missing");

it("should require the source", function () {
    $this->action
        ->setRedirectUrl("http://example.text")
        ->setReturnUrl("http://mysite.test")
        ->setClientId('test')
        ->perform();
})->throws(Exception::class, "Source Missing");

it("should redirect to the piano url", function () {
    $clientId = "test-id";
    $source = "test-source";
    $returnUrl = "http://mysite.test";
    $redirectUrl = "http://example.test";

    $params = [
        'response_type' => 'code',
        'client_id' => $clientId,
        'redirect_uri' => "{$returnUrl}",
        'disable_sign_up' => 'false',
    ];

    $url = $redirectUrl . '?' . http_build_query($params);

    $this->mockRedirect->shouldReceive('to')
            ->once()
            ->with($url);

    $this->action
        ->setRedirectUrl($redirectUrl)
        ->setReturnUrl($returnUrl)
        ->setClientId($clientId)
        ->setSource($source)
        ->perform();
});
