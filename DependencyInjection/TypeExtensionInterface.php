<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

interface TypeExtensionInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function getAccessTokenProviderDef(ContainerBuilder $container, array $config): Definition;

    /**
     * @param array<string, mixed> $config
     */
    public function getAuthorizationUriDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition;

    /**
     * @param array<string, mixed> $config
     */
    public function getMiddlewareDefs(ContainerBuilder $container, array $config, array $baseMiddlewares): array;
}
