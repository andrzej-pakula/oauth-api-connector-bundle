<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;

use ArrayIterator;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use LogicException;

final class MiddlewareAggregate implements IteratorAggregate
{
    private ArrayIterator $middleware;

    public function __construct(iterable $middleware)
    {
        $this->middleware = new ArrayIterator();
        $this->doMerge($middleware);
    }

    public function merge(iterable $middleware): self
    {
        $this->doMerge($middleware);

        return $this;
    }

    public function add(MiddlewareInterface $middleware, int $priority = 0): self
    {
        $this->middleware[$priority] = $middleware;

        return $this;
    }

    public function remove(MiddlewareInterface $middleware, ?int $searchPriority = null): self
    {
        $trash = [];
        foreach ($this->middleware as $priority => $handler) {
            if ($middleware === $handler) {
                $trash[$priority] = $handler;
            }
        }

        if (null === $searchPriority && count($trash) > 1) {
            throw new InvalidArgumentException('Priority parameter is required because more than one middleware instance was found.');
        }

        foreach ($trash as $priority => $handler) {
            unset($this->middleware[$priority]);
        }

        return $this;
    }

    public function getStack(): MiddlewareStackInterface
    {
         return new class($this->middleware) implements MiddlewareStackInterface {
             private ArrayIterator $middleware;

             public function __construct(ArrayIterator $middleware)
             {
                 $middleware->uksort(static function (int $a, int $b) {
                     return $a === $b ? 0 : ($a > $b ? -1 : 1);
                 });

                 $this->middleware  = $middleware;
             }

             public function next(): MiddlewareInterface
             {
                 if ($this->middleware->valid()) {
                     $current = $this->middleware->current();
                     $this->middleware->next();

                     return $current;
                 }

                 throw new LogicException('Unhandled response.');
             }
         };
    }

    public function getIterator(): Iterator
    {
        return $this->middleware;
    }

    private function doMerge(iterable $middleware): void
    {
        if (is_array($middleware)) {
            foreach ($middleware as [$handler, $priority]) {
                $this->add($handler, $priority);
            }
        } elseif ($middleware instanceof IteratorAggregate) {
            $this->middleware = $middleware->getIterator();
        }
    }
}
