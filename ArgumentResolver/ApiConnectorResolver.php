<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ArgumentResolver;


use Andreo\OAuthApiConnectorBundle\Security\ApiConnectorInterface;
use Andreo\OAuthApiConnectorBundle\Security\ApiConnectorRegistry;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ApiConnectorResolver implements ArgumentValueResolverInterface
{
    private ApiConnectorRegistry $apiConnectorRegistry;

    public function __construct(ApiConnectorRegistry $apiConnectorRegistry)
    {
        $this->apiConnectorRegistry = $apiConnectorRegistry;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ApiConnectorInterface::class !== $argument->getType() && !$this->apiConnectorRegistry->isEmpty();
    }

    /**
     * @return Generator<ApiConnectorInterface>d
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $clientName = $request->request->get('client_name');

        yield $this->apiConnectorRegistry->get($clientName);
    }
}