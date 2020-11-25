<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class CreateURIsMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        $parameters = $httpContext->getParameters();
        $zoneId = $parameters->hasZoneId() ? $parameters->getZoneId() : null;
        $clientContext = $clientContext->createRedirectUri($this->router, $zoneId);

        if ($httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $clientContext = $clientContext->createAuthorizationUri($clientContext->getRedirectUri());

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
