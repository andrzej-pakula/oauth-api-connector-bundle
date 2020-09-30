<?php

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class AndreoOAuthApiConnectorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
    }
}
