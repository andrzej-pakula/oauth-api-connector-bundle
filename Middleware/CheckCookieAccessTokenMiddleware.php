<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckCookieAccessTokenMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if ($attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $accessTokenStorageKey = AccessToken::getKey($attributeBag->getClientId());

        if (!$request->cookies->has($accessTokenStorageKey)) {
            return $stack->next()($request, $response, $stack);
        }

        $accessTokenCookie = $request->cookies->get($accessTokenStorageKey);
        AccessToken::decrypt($accessTokenCookie);

        return $response;
    }
}
