<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckCookieAccessTokenMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        if ($context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $accessTokenStorageKey = AccessToken::getKey($context->getClientId());

        if (!$request->cookies->has($accessTokenStorageKey)) {
            return $stack->next()($request, $response, $stack);
        }

        $accessTokenCookie = $request->cookies->get($accessTokenStorageKey);
        AccessToken::decrypt($accessTokenCookie);

        return $response;
    }
}
