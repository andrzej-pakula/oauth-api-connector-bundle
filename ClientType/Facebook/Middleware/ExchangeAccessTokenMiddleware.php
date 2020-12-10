<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\Middleware;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken\ExchangeAccessTokenQuery;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use Andreo\OAuthClientBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthClientBundle\Middleware\MiddlewareStackInterface;
use Symfony\Component\HttpFoundation\Response;

final class ExchangeAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthClientInterface $httpClient;

    public function __construct(OAuthClientInterface $httpClient)
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

        $query = ExchangeAccessTokenQuery::from($clientContext, $accessToken);

        $accessToken = $this->httpClient->getAccessToken($query);

        $httpContext->saveAccessToken($clientContext->getTokenStorageKey(), $accessToken);

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
