<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenProviderInterface;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Symfony\Component\HttpFoundation\Response;

final class GetAccessTokenMiddleware implements MiddlewareInterface
{
    private AccessTokenProviderInterface $accessTokenProvider;

    public function __construct(AccessTokenProviderInterface $accessTokenProvider)
    {
        $this->accessTokenProvider = $accessTokenProvider;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $accessToken = $this->accessTokenProvider->getAccessToken($httpContext, $clientContext);

        $httpContext->saveAccessToken($clientContext->getTokenStorageKey(), $accessToken);

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
