<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\Http;

use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\RefreshAccessTokenInterface;
use Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\ClientType\Facebook\User\User;
use Andreo\OAuthClientBundle\Http\OAuthHttpClientInterface;
use Andreo\OAuthClientBundle\User\Query\GetUserInterface;

final class FacebookHttpClient implements OAuthHttpClientInterface
{
    use ClientDecoratorTrait;

    public function getAccessToken(GetAccessTokenInterface $getAccessToken): AccessToken
    {
        return $this->get('oauth/access_token', [
            'data' => $getAccessToken,
        ])->getData();
    }

    public function refreshAccessToken(RefreshAccessTokenInterface $refreshAccessToken): AccessToken
    {
        return $this->get('oauth/access_token', [
            'data' => $refreshAccessToken,
        ])->getData();
    }

    public function getUser(GetUserInterface $getUser): User
    {
        return $this->get('me', [
            'data' => $getUser,
        ])->getData();
    }
}
