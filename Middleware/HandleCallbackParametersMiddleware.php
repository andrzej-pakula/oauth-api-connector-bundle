<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\State;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class HandleCallbackParametersMiddleware implements MiddlewareInterface
{
    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        $attributeBag = $attributeBag->handleCallback($request)->save($request);

        if (!$attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $stack);
        }

        if (!State::isStored($attributeBag->getClientId(), $request->getSession())) {
            throw new BadRequestHttpException('Missing state in current session.');
        }

        $sessionState = State::get($attributeBag->getClientId(), $request->getSession());

        if ($sessionState->equals($attributeBag->getCallbackParameters()->getState())) {
            return $stack->next()($request, $stack);
        }

        throw new RuntimeException('Invalid state.');
    }
}
