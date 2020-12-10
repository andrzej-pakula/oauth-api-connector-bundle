<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessTokenInterface;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use Symfony\Component\HttpFoundation\Response;

final class GetAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthClientInterface $httpClient;

    private GetAccessTokenInterface $accessTokenQuery;

    public function __construct(OAuthClientInterface $httpClient, GetAccessTokenInterface $accessTokenQuery)
    {
        $this->httpClient = $httpClient;
        $this->accessTokenQuery = $accessTokenQuery;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $query = $this->accessTokenQuery
            ->withRedirectUri($clientContext->getRedirectUri())
            ->withCode($httpContext->getCode());

        $accessToken = $this->httpClient->getAccessToken($query);

        $httpContext->saveAccessToken($clientContext->getTokenStorageKey(), $accessToken);

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
