<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\State;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class HandleCallbackParametersMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        $attributes = $attributes->handleCallback($request)->save($request);

        if (!$attributes->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        $sessionState = State::getFromStorage($attributes->getClientId(), $request->getSession());
        if (null === $sessionState) {
            throw new BadRequestHttpException('Missing state in current session.');
        }

        if ($sessionState->equals($attributes->getCallbackParameters()->getState())) {
            return $stack->next()($request, $stack);
        }

        throw new RuntimeException('Invalid state.');
    }
}
