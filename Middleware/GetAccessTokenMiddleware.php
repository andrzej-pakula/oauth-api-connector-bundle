<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use Andreo\OAuthClientBundle\AccessToken\Query\AccessTokenQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthClientInterface $httpClient;

    public function __construct(OAuthClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        if (!$context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $query = AccessTokenQuery::from($context);

        $accessToken = $this->httpClient->getAccessToken($query);

        $request->attributes->set(AccessToken::getKey($context->getClientId()), $accessToken);

        return $stack->next()($request, $response, $stack);
    }

}
