<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Controller;

use Andreo\OAuthClientBundle\Client\ClientInterface;
use Generator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ClientArgumentResolver implements ArgumentValueResolverInterface
{
    private ServiceLocator $clientLocator;

    public function __construct(ServiceLocator $clientLocator)
    {
        $this->clientLocator = $clientLocator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ClientInterface::class === $argument->getType() && !empty($request->attributes->get('client'));
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->clientLocator->get($request->attributes->get('client'));
    }
}
