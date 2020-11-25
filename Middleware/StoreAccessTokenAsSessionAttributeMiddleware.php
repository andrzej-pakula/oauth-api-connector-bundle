<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenAsSessionAttributeMiddleware implements MiddlewareInterface
{
    use GetAccessTokenFromAttributesTrait;

    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->fromAttributes($httpContext, $clientContext);

        $accessTokenStorageKey = $accessToken::getKey($clientContext->getClientName());

        $response = $httpContext->getResponse();
        $request = $httpContext->getRequest();

        $request->getSession()->set($accessTokenStorageKey, $accessToken->encrypt());

        $request->attributes->remove($accessTokenStorageKey);

        return $response;
    }
}
