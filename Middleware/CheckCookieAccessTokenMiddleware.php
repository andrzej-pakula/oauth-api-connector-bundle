<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Symfony\Component\HttpFoundation\Response;

final class CheckCookieAccessTokenMiddleware implements MiddlewareInterface
{
    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if ($httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $accessTokenStorageKey = AccessToken::getKey($clientContext->getClientName());

        $request = $httpContext->getRequest();
        if (!$request->cookies->has($accessTokenStorageKey)) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $accessTokenCookie = $request->cookies->get($accessTokenStorageKey);
        AccessToken::decrypt($accessTokenCookie);

        return $httpContext->getResponse();
    }
}
