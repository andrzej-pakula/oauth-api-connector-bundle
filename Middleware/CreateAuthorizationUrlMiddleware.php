<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attributes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateAuthorizationUrlMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        if ($attributes->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        $attributes->createAuthorizationUrl()->save($request);

        return $stack->next()($request, $stack);
    }
}
