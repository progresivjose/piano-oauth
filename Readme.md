# Introduction

The purpose of this package is to simplify the necessary steps to perform OAuth authentication for registered users in Piano.

## Usage

First, you need to instantiate the PianoOauth class as follows.


```php
use Progresivjose\PianoOauth;

$auth = new PianoOauth('AID', 'API_TOKEN', 'OAUTH_CLIENT_SECRET');
```

You can also specify the base url for the API Calls, by default the package use the sandbox URL *https://sandbox.tinypass.com*

```php
use Progresivjose\PianoOauth;

//Example with the Base URL
$auth = new PianoOauth('AID', 'API_TOKEN', 'OAUTH_CLIENT_SECRET', 'http://api.tinypass.io');
```

Then you can call the preAuth method, where you pass two arguments:

- returnUrl: The URL where Piano should return with the authentication code.
- redirectUrl (optional): The URL where the package will redirect for authenticate the user.
- source (optional): If you need to specify the source of the page that is calling it will concatenate as a query param in the returnUrl.

This method will redirect the user to Piano Login Page.

```php
$returnUrl = 'https://my-site.com/post-login';

$auth->preAuth($returnUrl);
```

And then, when the login form of piano redirects to your own page, you should capture the request query param called *code* and pass as an argument with the *returnUrl* to the postAuth method, this will return an User object with the data of the authenticated user, if it fails it should return null.

```php
$code = $_REQUEST['code'];
$returnUrl = 'https://my-site.com/post-login';

$user = $this->postAuth($code, $returnUrl);
```

### User Object

This are the methods of the User Object:

- getName: Returns the name of the authenticated user.
- getLastname: Returns the lastname of the authenticated user.
- getPersonalName: Returns the personal name of the authenticated user.
- getEmail: Returns the email of the authenticated user.