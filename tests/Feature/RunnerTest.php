<?php

use Progresivjose\PianoOauth\Actions\CheckAuthentication;
use Progresivjose\PianoOauth\Actions\GetAccessTokens;
use Progresivjose\PianoOauth\Actions\RedirectToLoginForm;
use Progresivjose\PianoOauth\Entities\AccessToken;
use Progresivjose\PianoOauth\Entities\Result;
use Progresivjose\PianoOauth\Entities\User;
use Progresivjose\PianoOauth\Responses\Response;
use Progresivjose\PianoOauth\Runners\Runner;

beforeEach(function () {
    $this->mockRedirectToLogin = Mockery::mock(RedirectToLoginForm::class);
    $this->mockGetAccessToken = Mockery::mock(GetAccessTokens::class);
    $this->mockCheckAuthentication = Mockery::mock(CheckAuthentication::class);
    $this->runner = new Runner(
        $this->mockRedirectToLogin,
        $this->mockGetAccessToken,
        $this->mockCheckAuthentication
    );
});

it("should redirect to the url with the given preauth", function () {
    $this->mockRedirectToLogin
        ->shouldReceive('setRedirectUrl')
        ->once()
        ->with('http://example.test')
        ->andReturnSelf()
        ->shouldReceive('setReturnUrl')
        ->once()
        ->with('http://return.test')
        ->andReturnSelf()
        ->shouldReceive('setClientId')
        ->once()
        ->with('test-aid')
        ->andReturnSelf()
        ->shouldReceive('setSource')
        ->once()
        ->with('')
        ->andReturnSelf()
        ->shouldReceive('perform')
        ->once();

    $this->runner
         ->setAid('test-aid')
         ->preAuth(
             redirectUrl: 'http://example.test',
             returnUrl: 'http://return.test',
         );
});

it("should return the user on postauth", function () {
    $accessToken = new AccessToken("test-access-token", "tests-refresh-token");
    $accessTokenResult = new Result($accessToken, AccessToken::class);
    $user = new User("test-uid", "John", "Doe", "John Doe", "john@doe.co");
    $userResult = new Result($user, User::class);

    $this->mockGetAccessToken
        ->shouldReceive('setCode')
        ->once("test-code")
        ->andReturnSelf()
        ->shouldReceive("setRedirectUrl")
        ->once()
        ->with("http://return.test")
        ->andReturnSelf()
        ->shouldReceive('perform')
        ->once()
        ->andReturn($accessTokenResult);

    $this->mockCheckAuthentication
        ->shouldReceive('setAccessToken')
        ->once()
        ->with('test-access-token')
        ->andReturnSelf()
        ->shouldReceive('setRefreshToken')
        ->once()
        ->with('tests-refresh-token')
        ->andReturnSelf()
        ->shouldReceive('perform')
        ->once()
        ->andReturn($userResult);

    expect($this->runner->postAuth('test-code', 'http://return.test'))
        ->toBe($user);
});

it("should return null on postauth if the access token is invalid", function () {
    $accessTokenResult = new Result('', Response::class);

    $this->mockGetAccessToken
        ->shouldReceive('setCode')
        ->once("test-code")
        ->andReturnSelf()
        ->shouldReceive("setRedirectUrl")
        ->once()
        ->with("http://return.test")
        ->andReturnSelf()
        ->shouldReceive('perform')
        ->once()
        ->andReturn($accessTokenResult);

    $this->mockCheckAuthentication
        ->shouldReceive('setAccessToken')
        ->never();

    expect($this->runner->postAuth('test-code', 'http://return.test'))
        ->toBe(null);
});

it("should return null on postauth if the user does not exist", function () {
    $accessToken = new AccessToken("test-access-token", "tests-refresh-token");
    $accessTokenResult = new Result($accessToken, AccessToken::class);
    $userResult = new Result("", Result::class);

    $this->mockGetAccessToken
        ->shouldReceive('setCode')
        ->once("test-code")
        ->andReturnSelf()
        ->shouldReceive("setRedirectUrl")
        ->once()
        ->with("http://return.test")
        ->andReturnSelf()
        ->shouldReceive('perform')
        ->once()
        ->andReturn($accessTokenResult);

    $this->mockCheckAuthentication
        ->shouldReceive('setAccessToken')
        ->once()
        ->with('test-access-token')
        ->andReturnSelf()
        ->shouldReceive('setRefreshToken')
        ->once()
        ->with('tests-refresh-token')
        ->andReturnSelf()
        ->shouldReceive('perform')
        ->once()
        ->andReturn($userResult);

    expect($this->runner->postAuth('test-code', 'http://return.test'))
        ->toBe(null);
});
