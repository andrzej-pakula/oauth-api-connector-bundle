<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class CreateCallbackUriMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        $attributes->createCallbackUri($this->router)->save($request);

        return $stack->next()($request, $stack);
    }
}
