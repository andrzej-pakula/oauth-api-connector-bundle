<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if ($httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        return new RedirectResponse($clientContext->getAuthorizationUri()->getUri());
    }
}
