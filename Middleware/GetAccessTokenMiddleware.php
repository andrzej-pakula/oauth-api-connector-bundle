<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Http\OAuthClientInterface;
use Andreo\OAuthApiConnectorBundle\Http\Query\AccessTokenQuery;
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
        $attributeBag = AttributeBag::get($request);
        if (!$attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $query = AccessTokenQuery::from($attributeBag);

        $accessToken = $this->httpClient->getAccessToken($query);

        $request->attributes->set(AccessToken::getKey($attributeBag->getClientId()), $accessToken);

        return $stack->next()($request, $response, $stack);
    }

}
