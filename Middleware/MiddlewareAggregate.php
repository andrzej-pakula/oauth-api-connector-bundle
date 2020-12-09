<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Exception\UnhandledResponseException;
use ArrayIterator;
use Iterator;
use IteratorAggregate;

final class MiddlewareAggregate implements MiddlewareAggregateInterface, IteratorAggregate
{
    private ArrayIterator $middleware;

    public function __construct(iterable $middleware = [])
    {
        $this->middleware = new ArrayIterator();

        $this->merge($middleware);
    }

    public function add(MiddlewareInterface $middleware, int $priority = 0): self
    {
        $this->middleware->append([$middleware, $priority]);

        return $this;
    }

    public function remove(MiddlewareInterface $middleware, ?int $priority = null): self
    {
        foreach ($this->middleware as $index => [$handler, $handlerPriority]) {
            if ($middleware === $handler && (null === $priority || $priority === $handlerPriority)) {
                unset($this->middleware[$index]);
            }
        }

        return $this;
    }

    public function merge(iterable $middlewareIterator): self
    {
        if ($middlewareIterator instanceof IteratorAggregate) {
            $middlewareIterator = $middlewareIterator->getIterator();
        }

        foreach ($middlewareIterator as $middleware) {
            $this->middleware->append($middleware);
        }

        return $this;
    }

    public function getStack(): MiddlewareStackInterface
    {
        return new class($this->middleware) implements MiddlewareStackInterface {
            private ArrayIterator $middleware;

            public function __construct(ArrayIterator $middleware)
            {
                $middleware->uasort(static function (array $first, array $second) {
                    [,$a] = $first;
                    [,$b] = $second;

                    return $a === $b ? 0 : ($a > $b ? -1 : 1);
                });

                $this->middleware  = $middleware;
            }

            public function next(): MiddlewareInterface
            {
                if ($this->middleware->valid()) {
                    [$current] = $this->middleware->current();
                    $this->middleware->next();

                    return $current;
                }

                throw new UnhandledResponseException();
            }
        };
    }

    public function getIterator(): Iterator
    {
        return $this->middleware;
    }
}
