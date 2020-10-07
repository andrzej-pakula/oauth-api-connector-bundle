<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;


use Andreo\OAuthApiConnectorBundle\Client\ClientFactoryInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ClientFactoryPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private string $clientTag;

    private string $middlewareTag;

    /**
     * @param string $clientTag
     */
    public function __construct(
        string $clientTag = 'andreo.oauth.client',
        string $middlewareTag = 'andreo.oauth_client.middleware')
    {
        $this->clientTag = $clientTag;
        $this->middlewareTag = $middlewareTag;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ClientFactoryInterface::class)) {
            return;
        }

        $taggedClientIds = $container->findTaggedServiceIds($this->clientTag);
        $taggedMiddlewareIds = $container->findTaggedServiceIds($this->middlewareTag);

        foreach ($taggedClientIds as $clientId => $clientTagAttributes) {
            $clientTagAttributes = array_merge(...$clientTagAttributes);

            $excludedMiddleware = [];
            foreach ($taggedMiddlewareIds as $middlewareId => $middlewareTagAttributes) {
                $middlewareTagAttributes = array_merge(...$middlewareTagAttributes);


                $middlewareClient = $middlewareTagAttributes['client'] ?? null;
                $middlewareType = $middlewareTagAttributes['type'] ?? null;

                if (null !== $middlewareClient && $middlewareClient !== $clientTagAttributes['name']) {
                    $excludedMiddleware[] = $middlewareId;

                } elseif (null !== $middlewareType &&  $middlewareType !== $clientTagAttributes['type']) {
                    $middlewareVersion = $middlewareTagAttributes['version'] ?? null;

                    if (null !== $clientVersion = $clientTagAttributes['version']) {

                        if (null === $middlewareVersion) {
                            $excludedMiddleware[] = $middlewareId;
                        } elseif ($clientVersion !== $middlewareVersion) {
                            $excludedMiddleware[] = $middlewareId;
                        }

                    } elseif (null !== $middlewareVersion) {
                        throw new RuntimeException("Client {$clientTagAttributes['name']} does not have any version.");
                    } else {
                        $excludedMiddleware[] = $middlewareId;
                    }
                }

                $middlewareDef = $container->getDefinition($middlewareId);
                if (null !== $decoratedServiceData = $middlewareDef->getDecoratedService()) {
                    $decoratedId = $decoratedServiceData[0];

                    $decoratedDef = $container->getDefinition($decoratedId);
                    $decoratedTagAttributes = array_merge(...$decoratedDef->getTag($this->middlewareTag));

                    if (null !== $priority = $decoratedTagAttributes['priority'] ?? null) {
                        $middlewareDef
                            ->clearTag($this->middlewareTag)
                            ->addTag($this->middlewareTag, ['priority' => $priority]);
                    }

                    $excludedMiddleware[] = $decoratedId;
                }
            }

            if (empty($excludedMiddleware)) {
                continue;
            }


            $middlewareReferences = $this->findAndSortTaggedServices($this->middlewareTag, $container);
            $clientMiddleware = [];
            foreach ($middlewareReferences as $middlewareReference) {
                if (!in_array((string)$middlewareReference, $excludedMiddleware, true)) {
                    $clientMiddleware[] = $middlewareReference;
                }
            }

            $clientDef = $container
                ->getDefinition($clientId)
                ->replaceArgument(1, new IteratorArgument($clientMiddleware));

            $container->setDefinition($clientId, $clientDef);
        }
    }
}
