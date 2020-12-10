<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if ($httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        return new RedirectResponse($clientContext->getAuthorizationUri()->getUri());
    }
}
