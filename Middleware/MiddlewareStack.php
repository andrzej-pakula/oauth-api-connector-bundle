<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;

use Iterator;
use LogicException;

final class MiddlewareStack implements MiddlewareStackInterface
{
    /** @var MiddlewareInterface[] */
    private array $middleware;

    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(iterable $middleware)
    {
        $this->middleware = $middleware;
    }

    public function next(): MiddlewareInterface
    {
        if (null !== $next = array_shift($this->middleware)) {
            return $next;
        }

        throw new LogicException('Response was not handled by middleware.');
    }

    /**
     * @param iterable<MiddlewareInterface> $middleware
     */
    public static function create(iterable $middleware): self
    {
        if ($middleware instanceof \IteratorAggregate) {
            $middleware = $middleware->getIterator();
        }

        if ($middleware instanceof Iterator) {
            $middleware = iterator_to_array($middleware);
        }


        return new self($middleware);
    }
}
