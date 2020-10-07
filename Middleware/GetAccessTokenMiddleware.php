<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Andreo\OAuthApiConnectorBundle\Http\OAuthClientInterface;
use Andreo\OAuthApiConnectorBundle\Http\Query\AccessTokenQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetAccessTokenMiddleware implements MiddlewareInterface
{
    private OAuthClientInterface $httpClient;

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        if (!$attributes->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        if (AccessToken::existAndValid($attributes->getClientId(), $request->getSession())) {
            return $stack->next()($request, $stack);
        }

        $query = AccessTokenQuery::fromAttributes($attributes);
        $accessToken = $this->httpClient->getAccessToken($query);

        $accessToken->store($attributes->getClientId(), $request->getSession());

        return $stack->next()($request, $stack);
    }
}
