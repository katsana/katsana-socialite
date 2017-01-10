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
     * Endpoint.
     *
     * @var string
     */
    protected static $endpoints = [
        'api' => 'https://api.katsana.com',
        'oauth' => 'https://my.katsana.com/oauth',
    ];

    /**
     * Set API endpoints.
     *
     * @param array $endpoints
     */
    public static function setEndpoint(array $endpoints)
    {
        static::$endpoints = array_merge(static::$endpoints, $endpoints);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            static::$endpoints['oauth'].'/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return static::$endpoints['oauth'].'/token';
    }
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            static::$endpoints['api'].'/profile', [
            'headers' => [
                'Accept' => 'application/vnd.KATSANA.v1+json',
                'Authorization' => "Bearer {$token}",
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
