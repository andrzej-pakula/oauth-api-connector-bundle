<?php

declare(strict_types=1);


namespace Tests\Andreo\OAuthApiConnectorBundle\App\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareStackInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreStateMiddleware implements MiddlewareInterface
{
    private MiddlewareInterface $decorated;

    public function __construct(MiddlewareInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        if ($attributes->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        $attributes->getState()->store($attributes->getClientId(), $request->getSession());

        return $stack->next()($request, $stack);
    }
}
