<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\Client\RequestContext\State;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateReturningStateMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        if (!$context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $stateStorageKey = State::getKey($context->getClientId());
        if (!$request->getSession()->has($stateStorageKey)) {
            throw new RuntimeException('Missing state in current session.');
        }

        $sessionState = State::decrypt($request->getSession()->get($stateStorageKey));

        if ($sessionState->equals($context->getParameters()->getState())) {
            $request->getSession()->remove($sessionState::getKey($context->getClientId()));

            return $stack->next()($request, $response, $stack);
        }

        throw new RuntimeException('Invalid state.');
    }
}
