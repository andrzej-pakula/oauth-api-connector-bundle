<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\Middleware;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken\RefreshAccessTokenQuery;
use Andreo\OAuthClientBundle\Http\OAuthHttpClientInterface;
use Andreo\OAuthClientBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthClientBundle\Middleware\MiddlewareStackInterface;
use Symfony\Component\HttpFoundation\Response;

final class RefreshAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthHttpClientInterface $httpClient;

    public function __construct(OAuthHttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $httpContext->getAccessToken($clientContext->getTokenStorageKey());

        $query = new RefreshAccessTokenQuery(
            $clientContext->getId()->getId(),
            $clientContext->getSecret()->getSecret(),
            $accessToken->getAccessToken()
        );

        $accessToken = $this->httpClient->refreshAccessToken($query);

        $httpContext->saveAccessToken($clientContext->getTokenStorageKey(), $accessToken);

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
