KATSANA Socialite Provider
==============

[![Build Status](https://travis-ci.org/katsana/katsana-socialite.svg?branch=1.0)](https://travis-ci.org/katsana/katsana-socialite)
[![Latest Stable Version](https://poser.pugx.org/katsana/socialite/v/stable)](https://packagist.org/packages/katsana/socialite)
[![Total Downloads](https://poser.pugx.org/katsana/socialite/downloads)](https://packagist.org/packages/katsana/socialite)
[![Latest Unstable Version](https://poser.pugx.org/katsana/socialite/v/unstable)](https://packagist.org/packages/katsana/socialite)
[![License](https://poser.pugx.org/katsana/socialite/license)](https://packagist.org/packages/katsana/socialite)


* [Installation](#installation)


## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "katsana/socialite": "^1.0"
    }
}
```

## Official Documentation

### Configuration

KATSANA Socialite is built using [SocialiteProviders](http://socialiteproviders.github.io/). First, you need to register the service provides in your `config/app.php` configuration file:

```php
'providers' => [
    
    // Other service providers...
    Katsana\ServiceProvider::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,

],
```

Also, add the `Socialite` facade to the `aliases` array in your app configuration file:

```php
'Katsana' => Katsana\Katsana::class,
'Socialite' => Laravel\Socialite\Facades\Socialite::class,
```

You will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your `config/services.php` configuration file. For example:

```php
'katsana' => [
    'environment' => 'production',
    'client_id' => 'your-katsana-client-id',
    'client_secret' => 'your-katsana-client-secret',
    'redirect' => 'http://your-callback-url',
    //Optional
    'endpoints'=>[
        'api' => 'http://katsana-api-endpoint',
        'oauth' => 'http://katsana-outh-endpoint',
    ],
],
```

Finally, you need to add `Katsana\Socialite\Bootstrap` to be triggered by `SocialiteProviders\Manager\SocialiteWasCalled` event. To do so, edit your `App\Providers\EventServiceProvider`.

```php
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Other events...
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \Katsana\Socialite\Bootstrap::class,
        ],
    ];
```

### Basic Usage

Next, you are ready to authenticate users! You will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the callback from the provider after authentication. We will access Socialite using the `Socialite` facade:

```php
<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;

class PassportController extends Controller
{
    /**
     * Redirect the user to the KATSANA authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('katsana')->redirect();
    }

    /**
     * Obtain the user information from KATSANA.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $passport = Socialite::driver('katsana')->user();

        // $passport->token;
    }
}
```

The `redirect` method takes care of sending the user to the OAuth provider, while the `user` method will read the incoming request and retrieve the user's information from the provider. Before redirecting the user, you may also set "scopes" on the request using the `scope` method. This method will overwrite all existing scopes:

```php
return Socialite::driver('katsana')
            ->scopes(['scope1', 'scope2'])->redirect();
```

Of course, you will need to define routes to your controller methods:

```php
Route::get('passport', 'Auth\PassportController@redirectToProvider');
Route::get('passport/callback', 'Auth\PassportController@handleProviderCallback');
```

A number of OAuth providers support optional parameters in the redirect request. To include any optional parameters in the request, call the `with` method with an associative array:

```php
return Socialite::driver('katsana')
            ->with(['hd' => 'example.com'])->redirect();
```

When using the `with` method, be careful not to pass any reserved keywords such as `state` or `response_type`.

#### Stateless Authentication

The `stateless` method may be used to disable session state verification. This is useful when adding social authentication to an API:

```php
return Socialite::driver('katsana')->stateless()->user();
```


#### Retrieving User Details

Once you have a user instance, you can grab a few more details about the user:

```php
$passport = Socialite::driver('katsana')->user();

// OAuth Two Providers
$token = $passport->token;
$refreshToken = $passport->refreshToken; // not always provided
$expiresIn = $passport->expiresIn;

// Helper methods.
$passport->getId();
$passport->getName();
$passport->getEmail();
$passport->getAvatar();
$passport->getRaw();
```

#### Retrieving User Details From Token

If you already have a valid access token for a user, you can retrieve their details using the `userFromToken` method:

```php
$passport = Socialite::driver('katsana')->userFromToken($token);
```
