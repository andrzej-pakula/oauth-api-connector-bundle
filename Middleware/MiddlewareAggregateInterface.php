<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

interface MiddlewareAggregateInterface
{
    public function add(MiddlewareInterface $middleware, int $priority = 0): self;

    public function remove(MiddlewareInterface $middleware, ?int $priority = null): self;

    public function merge(iterable $middlewareIterator): self;

    public function getStack(): MiddlewareStackInterface;
}
