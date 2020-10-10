<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetStoredAccessTokenMiddleware implements MiddlewareInterface
{
    private MiddlewareInterface $responseHandler;

    public function __construct(MiddlewareInterface $responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);

        if (AccessToken::isStoredAndValid($attributeBag->getClientId(), $request->getSession())) {
            return ($this->responseHandler)($request, $stack);
        }

        return $stack->next()($request, $stack);
    }
}
