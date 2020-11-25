<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;


use Andreo\OAuthClientBundle\Middleware\MiddlewareAggregate;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Client implements ClientInterface
{
    private MiddlewareAggregate $middlewareAggregate;

    private ClientContext $clientContext;

    public function __construct(MiddlewareAggregate $middlewareAggregate, ClientContext $clientContext)
    {
        $this->middlewareAggregate = $middlewareAggregate;
        $this->clientContext = $clientContext;
    }

    public function handle(Request $request): Response
    {
        $stack = $this->middlewareAggregate->getStack();

        $httpContext = new HTTPContext($request, new RedirectResponse('/'));

        return $stack->next()($httpContext, $this->clientContext, $stack);
    }
}
