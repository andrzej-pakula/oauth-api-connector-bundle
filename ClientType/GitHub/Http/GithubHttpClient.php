<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\GitHub\Http;

use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\RefreshAccessTokenInterface;
use Andreo\OAuthClientBundle\ClientType\GitHub\User\User;
use Andreo\OAuthClientBundle\Http\OAuthHttpClientInterface;
use Andreo\OAuthClientBundle\User\Query\GetUserInterface;

final class GithubHttpClient implements OAuthHttpClientInterface
{
    use ClientDecoratorTrait;

    public function getAccessToken(GetAccessTokenInterface $getAccessToken): AccessToken
    {
        return $this->post('https://github.com/login/oauth/access_token', [
            'data' => $getAccessToken,
        ])->getData();
    }

    public function refreshAccessToken(RefreshAccessTokenInterface $refreshAccessToken): AccessToken
    {
        return $this->post('https://github.com/login/oauth/access_token', [
            'data' => $refreshAccessToken,
        ])->getData();
    }

    public function getUser(GetUserInterface $getUser): User
    {
        return $this->get('user', [
            'data' => $getUser,
            'headers' => [
                'Authorization' => 'Bearer '.$getUser->getAccessToken(),
            ],
        ])->getData();
    }
}
