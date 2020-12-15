<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\GitHub\Http;

use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\RefreshAccessTokenInterface;
use Andreo\OAuthClientBundle\Http\OAuthHttpClientInterface;

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
        return $this->get('https://github.com/login/oauth/access_token', [
            'data' => $refreshAccessToken,
        ])->getData();
    }
}
