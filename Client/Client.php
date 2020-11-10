<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;


use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\Middleware\MiddlewareAggregate;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Client implements ClientInterface
{
    private Context $context;

    private MiddlewareAggregate $middlewareAggregate;

    public function __construct(Context $context, MiddlewareAggregate $middlewareAggregate)
    {
        $this->context = $context;
        $this->middlewareAggregate = $middlewareAggregate;
    }

    public function handle(Request $request): Response
    {
        $this->context->handleRequest($request)->save($request);

        $stack = $this->middlewareAggregate->getStack();

        return $stack->next()($request, new RedirectResponse('/'), $stack);
    }
}
