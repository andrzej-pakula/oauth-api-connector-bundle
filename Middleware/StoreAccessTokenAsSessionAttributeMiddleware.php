<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenAsSessionAttributeMiddleware implements MiddlewareInterface
{
    use StoreAccessTokenTrait;

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if (!$attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->getAccessToken($request, $attributeBag);

        $accessTokenStorageKey = AccessToken::getKey($attributeBag->getClientId());
        $request->getSession()->set($accessTokenStorageKey, $accessToken->encrypt());
        $request->attributes->remove($accessTokenStorageKey);

        return $response;
    }
}
