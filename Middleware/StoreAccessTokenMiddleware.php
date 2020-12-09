<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Storage\StorableInterface;
use Andreo\OAuthClientBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Response;

final class StoreAccessTokenMiddleware implements MiddlewareInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        /** @var StorableInterface $accessToken */
        $accessToken = $httpContext->getAccessToken($clientContext->getTokenStorageKey());

        $this->storage->store($httpContext, $clientContext->getTokenStorageKey(), $accessToken);

        $httpContext->removeAccessToken($clientContext->getTokenStorageKey());

        return $httpContext->getResponse();
    }
}
