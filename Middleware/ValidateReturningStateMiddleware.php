<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AuthorizationUri\State;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Exception\InvalidStateException;
use Andreo\OAuthClientBundle\Exception\MissingStateException;
use Andreo\OAuthClientBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Response;

final class ValidateReturningStateMiddleware implements MiddlewareInterface
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

        if (!$this->storage->has($httpContext, $clientContext->getStateStorageKey())) {
            throw new MissingStateException();
        }

        $state = $this->storage->restore($httpContext, $clientContext->getStateStorageKey());
        if (!$state instanceof State) {
            throw new MissingStateException();
        }

        if ($state->equals($httpContext->getState())) {
            $this->storage->delete($httpContext, $clientContext->getStateStorageKey());

            return $stack->next()($httpContext, $clientContext, $stack);
        }

        throw new InvalidStateException();
    }
}
