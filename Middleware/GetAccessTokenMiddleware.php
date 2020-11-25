<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\AccessTokenQueryInterface;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use Symfony\Component\HttpFoundation\Response;

final class GetAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthClientInterface $httpClient;

    private AccessTokenQueryInterface $accessTokenQuery;

    public function __construct(OAuthClientInterface $httpClient, AccessTokenQueryInterface $accessTokenQuery)
    {
        $this->httpClient = $httpClient;
        $this->accessTokenQuery = $accessTokenQuery;
    }

    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $query = $this->accessTokenQuery
            ->withRedirectUri($clientContext->getRedirectUri())
            ->withCode($httpContext->getParameters()->getCode());

        $accessToken = $this->httpClient->getAccessToken($query);

        $request = $httpContext->getRequest();
        $request->attributes->set(AccessToken::getKey($clientContext->getClientName()), $accessToken);

        return $stack->next()($httpContext, $clientContext, $stack);
    }

}
