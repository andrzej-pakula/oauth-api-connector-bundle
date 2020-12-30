<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\AccessToken;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenProviderInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\ClientType\GitHub\User\GetUser;
use Andreo\OAuthClientBundle\Http\OAuthHttpClientInterface;

final class AccessTokenProvider implements AccessTokenProviderInterface
{
    private OAuthHttpClientInterface $httpClient;

    public function __construct(OAuthHttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getAccessToken(HttpContext $httpContext, ClientContext $clientContext): AccessTokenInterface
    {
        $query = new GetAccessToken(
            $clientContext->getId(),
            $clientContext->getSecret(),
            $httpContext->getCode(),
            $clientContext->getRedirectUri()
        );

        $accessToken = $this->httpClient->getAccessToken($query);

        $user = $this->httpClient->getUser(
            new GetUser($accessToken->getAccessToken())
        );

        return $accessToken->withUser($user);
    }
}
