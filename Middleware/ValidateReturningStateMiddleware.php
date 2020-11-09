<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\State;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateReturningStateMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if (!$attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $stateStorageKey = State::getKey($attributeBag->getClientId());
        if (!$request->getSession()->has($stateStorageKey)) {
            throw new RuntimeException('Missing state in current session.');
        }

        /** @var State $sessionState */
        $sessionState = State::decrypt($request->getSession()->get($stateStorageKey));

        if ($sessionState->equals($attributeBag->getParameters()->getState())) {
            return $stack->next()($request, $response, $stack);
        }

        throw new RuntimeException('Invalid state.');
    }
}
