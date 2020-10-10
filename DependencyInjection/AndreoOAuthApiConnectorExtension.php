<?php

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\Zone;
use Andreo\OAuthApiConnectorBundle\Client\ClientFactoryInterface;
use Andreo\OAuthApiConnectorBundle\Client\ClientInterface;
use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderInterface;
use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderRegistry;
use Andreo\OAuthApiConnectorBundle\Http\Provider\ApiConfigProviderFactory;
use Andreo\OAuthApiConnectorBundle\Middleware\GetAccessTokenMiddleware;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareInterface;
use Andreo\OAuthApiConnectorBundle\Middleware\SuccessfulResponseMiddleware;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class AndreoOAuthApiConnectorExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        foreach ($config as $type => $options) {
            foreach ($options['clients'] ?? [] as $name => $config) {
                $version = $config['version'] ?? null;

                $attributeDef = (new Definition(AttributeBag::class))
                    ->setPublic(false)
                    ->setFactory([AttributeBag::class, 'createFrom'])
                    ->setArguments([
                        [
                            'client_id' => $config['auth']['id'],
                            'client_name' => $name,
                            'client_secret' => $config['auth']['secret'],
                            'type' => $type,
                            'version' => $version,
                            'callback_uri' => 'andreo.oauth.client.authorize'
                        ],
                        new Reference(MetaDataProviderRegistry::class)
                    ]);

                $clientDef = (new Definition(ClientInterface::class))
                    ->setPublic(false)
                    ->setFactory(new Reference(ClientFactoryInterface::class))
                    ->setArguments([
                        $attributeDef,
                        new TaggedIteratorArgument('andreo.oauth_client.middleware'),
                    ])
                    ->addTag('andreo.oauth.client', [
                        'name' => $name,
                        'type' => $type,
                        'version' => $version
                    ]);

                $container->setDefinition(ClientInterface::class . '_' . $name, $clientDef);

                $zones = [];
                foreach ($config['zones'] ?? [] as $id => $zoneConfig) {
                    $zoneDef = (new Definition(Zone::class))
                        ->setFactory([Zone::class, 'fromConfig'])
                        ->setPublic(false)
                        ->addArgument(
                            [
                                'zone_id' => $id,
                                'successful_response_uri' => $zoneConfig['successful_response_uri']
                            ]
                        );

                    $zones[$id] = $zoneDef;
                }

                if (!empty($zones)) {
                    $successfulResponseMiddlewareDef = (new Definition(SuccessfulResponseMiddleware::class))
                        ->setDecoratedService(SuccessfulResponseMiddleware::class)
                        ->setPublic(false)
                        ->setArguments([
                            new Reference('router'),
                            $zones
                        ])
                        ->addTag('andreo.oauth_client.middleware', [
                            'client' => $name,
                        ]);

                    $container->setDefinition(SuccessfulResponseMiddleware::class . '_' . $name, $successfulResponseMiddlewareDef);
                }

                $getAccessTokenMiddlewareDef = (new Definition(GetAccessTokenMiddleware::class))
                    ->addArgument(new Reference($this->getHttpClientId($name)))
                    ->addTag('andreo.oauth_client.middleware', [
                        'client' => $name,
                        'priority' => 800
                    ]);

                $container->setDefinition(GetAccessTokenMiddleware::class. '_' . $name, $getAccessTokenMiddlewareDef);
            }
        }

        $this->registerAutoconfiguration($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles['AndreoGuzzleBundle'])) {
            throw new RuntimeException('');
        }

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $guzzleClientConfigs = [];
        foreach ($config as $type => $options) {
            foreach ($options['clients'] ?? [] as $name => $config) {
                $guzzleConfigProviderDef = (new Definition(ConfigProviderInterface::class))
                    ->setPublic(false)
                    ->setFactory(new Reference(ApiConfigProviderFactory::class))
                    ->setArguments([
                        $type,
                        $client['version'] ?? null
                    ]);

                $guzzleConfigProviderId = "andreo.guzzle.oauth.client.$name.config_provider";
                $container->setDefinition($guzzleConfigProviderId, $guzzleConfigProviderDef);

                $httpClientDef = (new Definition($config['http_client_id']))
                    ->setPublic(false);
                $httpClientId = $this->getHttpClientId($name);
                $container->setDefinition($httpClientId, $httpClientDef);

                $guzzleClientConfigs['clients'][$name] = [
                    'config_provider_id' => $guzzleConfigProviderId,
                    'decorator_id' => $httpClientId
                ];
            }
        }

        $container->prependExtensionConfig('andreo_guzzle', $guzzleClientConfigs);
    }

    private function getHttpClientId(string $clientName): string
    {
        return "andreo.oauth.http_client_$clientName";
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(MetaDataProviderInterface::class)
            ->addTag('andreo.oauth_client.meta_data_provider');

        $container
            ->registerForAutoconfiguration(MiddlewareInterface::class)
            ->addTag('andreo.oauth_client.middleware');
    }

}
