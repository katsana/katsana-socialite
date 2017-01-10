<?php

namespace Katsana\Socialite;

use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

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
     * @param array $endpoint
     */
    public static function setEndpoint(array $endpoint)
    {
        static::$endpoints = array_merge(static::$endpoints, $endpoints);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            static::$oauthEndpoint.'/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return static::$oauthEndpoint.'/token';
    }
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            static::$apiEndpoint.'/profile', [
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
