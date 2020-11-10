<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\Client\RequestContext\State;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class StoreStateMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        if ($context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $request->getSession()->set(
            State::getKey($context->getClientId()),
            $context->getState()->encrypt()
        );

        return $stack->next()($request, $response, $stack);
    }
}
