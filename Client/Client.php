<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Client implements ClientInterface
{
    /**
     * @var iterable<MiddlewareInterface>
     */
    private iterable $middleware;

    private Attributes $attributes;

    /**
     * @param iterable<MiddlewareInterface> $middleware
     */
    public function __construct(iterable $middleware, Attributes $attributes)
    {
        $this->middleware = $middleware;
        $this->attributes = $attributes;
    }

    public function connect(Request $request): Response
    {
        $this->attributes->save($request);
        $stack = MiddlewareStack::create($this->middleware);

        return $stack->next()($request, $stack);
    }
}
