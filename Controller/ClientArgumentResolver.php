<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Controller;


use Andreo\OAuthApiConnectorBundle\Client\ClientInterface;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ClientArgumentResolver implements ArgumentValueResolverInterface
{
    private ServiceLocator $clientLocator;

    public function __construct(ServiceLocator $clientLocator)
    {
        $this->clientLocator = $clientLocator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === ClientInterface::class &&
            $request->attributes->has('client_name');
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->clientLocator->get($request->attributes->get('client_name'));
    }
}
