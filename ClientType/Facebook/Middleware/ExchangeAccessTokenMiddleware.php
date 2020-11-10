<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\Middleware;


use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken\ExchangeAccessTokenQuery;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
use Andreo\OAuthClientBundle\Middleware\AccessTokenAttributeTrait;
use Andreo\OAuthClientBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthClientBundle\Middleware\MiddlewareStackInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExchangeAccessTokenMiddleware implements MiddlewareInterface
{
    use AccessTokenAttributeTrait;

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

        /** @var AccessToken $accessToken */
        $accessToken = $this->fromAttributes($request, $context);

        $query = ExchangeAccessTokenQuery::from($context, $accessToken);

        $accessToken = $this->httpClient->getAccessToken($query);

        $request->attributes->set(AccessToken::getKey($context->getClientId()), $accessToken);

        return $stack->next()($request, $response, $stack);
    }
}
