<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\AuthorizationUri\State;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HTTPContext;
use Andreo\OAuthClientBundle\Exception\InvalidStateException;
use Andreo\OAuthClientBundle\Exception\MissingStateException;
use Symfony\Component\HttpFoundation\Response;

final class ValidateReturningStateMiddleware implements MiddlewareInterface
{
    public function __invoke(HTTPContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response
    {
        if (!$httpContext->isCallback()) {
            return $stack->next()($httpContext, $clientContext, $stack);
        }

        $request = $httpContext->getRequest();

        $stateStorageKey = State::getKey($clientContext->getClientName());
        if (!$request->getSession()->has($stateStorageKey)) {
            throw new MissingStateException();
        }

        $sessionState = State::decrypt($request->getSession()->get($stateStorageKey));
        if ($sessionState->equals($httpContext->getParameters()->getState())) {
            $request->getSession()->remove($sessionState::getKey($clientContext->getClientName()));

            return $stack->next()($httpContext, $clientContext, $stack);
        }

        throw new InvalidStateException();
    }
}
