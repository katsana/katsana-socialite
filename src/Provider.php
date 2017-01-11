<?php

namespace Katsana\Socialite;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\User;
use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'KATSANA';

    /**
     * List of scopes.
     *
     * @var array
     */
    protected $scopes = ['*'];

    /**
     * Environment setting.
     *
     * @var string|null
     */
    protected static $environment;

    /**
     * Endpoint.
     *
     * @var string
     */
    protected static $endpoints = [
        'production' => [
            'api' => 'https://api.katsana.com',
            'oauth' => 'https://my.katsana.com/oauth',
        ],
        'carbon' => [
            'api' => 'https://carbon.api.katsana.com',
            'oauth' => 'https://carbon.katsana.com/oauth',
        ],
    ];

    /**
     * Set API environment.
     *
     * @param string|null $environment
     */
    public static function setEnvironment($environment = null)
    {
        static::$environment = $environment;
    }

    /**
     * Get environment endpoint.
     *
     * @param  string|null  $group
     *
     * @return array
     */
    protected function getEnvironmentEndpoint($group = null)
    {
        if (is_null($environment = static::$environment)) {
            $environment = $this->getConfig('environment', 'production');
        }

        if (is_null($group) || empty($group)) {
            return static::$endpoints[$environment];
        }

        return Arr::get(static::$endpoints, "{$environment}.{$group}");
    }


    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            $this->getEnvironmentEndpoint('oauth').'/authorize', $state
        );
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return $this->getEnvironmentEndpoint('oauth').'/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     *
     * @return array
     */
    protected function getUserByToken($token)
    {
        return $this->getSdkClient()
                    ->useCustomApiEndpoint($this->getEnvironmentEndpoint('api'))
                    ->setAccessToken($token)
                    ->resource('Profile')
                    ->show()
                    ->toArray();
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Laravel\Socialite\Two\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['fullname'],
            'email' => $user['email'],
            'avatar' => Arr::get($user, 'avatar.url'),
        ]);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * Additional config keys.
     *
     * @return array
     */
    public static function additionalConfigKeys()
    {
        return ['environment'];
    }

    /**
     * Get KATSANA SDK Client.
     *
     * @return \Katsana\Sdk\Client
     */
    protected function getSdkClient()
    {
        $app = Container::getInstance();

        if ($app->bound('katsana')) {
            return $app->make('katsana');
        }

        return Client::make($this->clientId, $this->clientSecret);
    }
}
