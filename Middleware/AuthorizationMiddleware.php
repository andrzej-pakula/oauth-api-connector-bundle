<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        if ($attributes->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        return new RedirectResponse($attributes->getAuthorizationUri()->getUri());
    }
}
