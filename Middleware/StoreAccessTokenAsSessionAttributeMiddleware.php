<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenAsSessionAttributeMiddleware implements MiddlewareInterface
{
    use AccessTokenAttributeTrait;

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        if (!$context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->fromAttributes($request, $context);

        $accessTokenStorageKey = $accessToken::getKey($context->getClientId());
        $request->getSession()->set($accessTokenStorageKey, $accessToken->encrypt());

        $request->attributes->remove($accessTokenStorageKey);

        return $response;
    }
}
