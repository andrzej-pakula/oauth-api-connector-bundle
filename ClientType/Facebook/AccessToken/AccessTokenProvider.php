<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenProviderInterface;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\ClientType\Facebook\User\Fields;
use Andreo\OAuthClientBundle\ClientType\Facebook\User\GetUser;
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
            new GetUser(
                $accessToken->getAccessToken(),
                [Fields::ID, Fields::FIRST_NAME, Fields::LAST_NAME,  Fields::EMAIL]
            )
        );

        return $accessToken->withUser($user);
    }
}
