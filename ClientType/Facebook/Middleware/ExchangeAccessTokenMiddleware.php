<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\Middleware;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken\ExchangeAccessTokenQuery;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use Andreo\OAuthClientBundle\Middleware\GetAccessTokenFromAttributesTrait;
use Andreo\OAuthClientBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthClientBundle\Middleware\MiddlewareStackInterface;
use Symfony\Component\HttpFoundation\Response;

final class ExchangeAccessTokenMiddleware implements MiddlewareInterface
{
    use GetAccessTokenFromAttributesTrait;

    private OAuthClientInterface $httpClient;

    public function __construct(OAuthClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        /** @var AccessToken $accessToken */
        $accessToken = $this->fromAttributes($httpContext, $clientContext);

        $query = ExchangeAccessTokenQuery::from($clientContext, $accessToken);

        $accessToken = $this->httpClient->getAccessToken($query);

        $request->attributes->set(AccessToken::getKey($context->getClientId()), $accessToken);

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
