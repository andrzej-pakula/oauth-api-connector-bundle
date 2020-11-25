<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\Http;


use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\AccessTokenQueryInterface;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use GuzzleHttp\ClientInterface;

final class OAuthHTTPClient implements OAuthClientInterface, ClientInterface
{
    use ClientDecoratorTrait;

    public function getAccessToken(AccessTokenQueryInterface $accessTokenQuery): AccessTokenInterface
    {
        return $this->get('oauth/access_token', [
            'data' => $accessTokenQuery
        ])->getData();
    }
}
