<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareAggregate;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Client implements ClientInterface
{
    private AttributeBag $attributeBag;

    private MiddlewareAggregate $middlewareAggregate;

    public function __construct(AttributeBag $attributeBag, MiddlewareAggregate $middlewareAggregate)
    {
        $this->attributeBag = $attributeBag;
        $this->middlewareAggregate = $middlewareAggregate;
    }

    public function handle(Request $request): Response
    {
        $this->attributeBag->handleRequest($request)->save($request);

        $stack = $this->middlewareAggregate->getStack();

        return $stack->next()($request, new RedirectResponse('/'), $stack);
    }
}
