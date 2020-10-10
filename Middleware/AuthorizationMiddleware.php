<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if ($attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        return new RedirectResponse($attributeBag->getAuthorizationUri()->getUri());
    }
}
