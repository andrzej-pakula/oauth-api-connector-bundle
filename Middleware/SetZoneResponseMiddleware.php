<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Exception\MissingZoneException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class SetZoneResponseMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$clientContext->hasZones()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        if (!$httpContext->isZoneSet()) {
            throw new MissingZoneException('Missing zone parameter. Check your template link definition.');
        }

        $zone = $clientContext->getZone($httpContext->getZone());
        $uri = $this->router->generate($zone->getSuccessfulResponseUri());

        $httpContext = $httpContext->withResponse(new RedirectResponse($uri));

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
