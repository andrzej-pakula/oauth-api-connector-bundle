<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class CreateURIsMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    private StorageInterface $storage;

    public function __construct(RouterInterface $router, StorageInterface $storage)
    {
        $this->router = $router;
        $this->storage = $storage;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        $zoneId = $httpContext->isZoneSet() ? $httpContext->getZone() : null;
        $clientContext = $clientContext->createRedirectUri($this->router, $zoneId);

        if ($httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $clientContext = $clientContext->createAuthorizationUri($clientContext->getRedirectUri());
        $state = $clientContext->getAuthorizationUri()->getState();

        $this->storage->store($httpContext, $clientContext->getStateStorageKey(), $state);

        return $stack->next()($httpContext, $clientContext, $stack);
    }
}
