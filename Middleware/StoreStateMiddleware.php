<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreStateMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if ($attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        $attributeBag->getState()->store($attributeBag->getClientId(), $request->getSession());

        return $stack->next()($request, $stack);
    }
}
