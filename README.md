KATSANA Socialite Provider
==============

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
        "katsana/socialite": "~0.1"
    }
}
```


## Configuration

### Service Provider

KATSANA Socialite is built using [SocialiteProviders](http://socialiteproviders.github.io/). First, you need to register the service provides in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...
    Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
],
```

Also, add the `Socialite` facade to the `aliases` array in your app configuration file:

```php
'Socialite' => Laravel\Socialite\Facades\Socialite::class,
```

You will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your `config/services.php` configuration file. For example:

```php
'katsana' => [
    'client_id' => 'your-katsana-client-id',
    'client_secret' => 'your-katsana-client-secret',
    'redirect' => 'http://your-callback-url',
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
