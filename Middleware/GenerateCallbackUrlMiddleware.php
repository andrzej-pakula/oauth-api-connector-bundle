<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attributes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class GenerateCallbackUrlMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        $attributes->generateCallbackUrl($this->router)->save($request);

        return $stack->next()($request, $stack);
    }
}
