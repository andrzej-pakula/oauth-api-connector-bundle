<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Storage\StorageInterface;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Symfony\Component\HttpFoundation\Response;

final class TryRestoreAccessTokenMiddleware implements MiddlewareInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if ($httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        if (!$this->storage->has($httpContext, $clientContext->getTokenStorageKey())) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $this->storage->restore($httpContext, $clientContext->getTokenStorageKey());

        return $httpContext->getResponse();
    }
}
