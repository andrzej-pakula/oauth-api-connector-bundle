<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

interface TypeExtensionInterface
{
    public function getAccessTokenQueryDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition;

    public function getAuthorizationUriDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition;

    /**
     * @return iterable<Definition>
     */
    public function getMiddlewareDefs(ContainerBuilder $container, array $config, array &$baseMiddlewares): iterable;
}
