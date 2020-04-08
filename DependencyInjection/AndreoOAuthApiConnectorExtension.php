<?php

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Andreo\Component\Messaging\Configurator\ConfiguratorFactoryFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class AndreoOAuthApiConnectorExtension extends Extension
{
    private const SUPPORTED_CLIENTS = [
        'facebook',
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        foreach (self::SUPPORTED_CLIENTS as $name) {
            $this->registerConfiguration($container, $config['clients'][$name] ?? []);
        }
    }

    private function registerConfiguration(ContainerBuilder $container, array $clientsConfigs): void
    {
        foreach ($clientsConfigs as $clientsConfig) {
            foreach ($clientsConfig as $name => $clientConfig) {

            }
        }
    }
}
