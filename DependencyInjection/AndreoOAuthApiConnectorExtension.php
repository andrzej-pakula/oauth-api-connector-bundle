<?php

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Client\Client;
use Andreo\OAuthApiConnectorBundle\Http\Provider\HttpClientConfigProvider;
use Andreo\OAuthApiConnectorBundle\Middleware\CheckSessionAccessTokenMiddleware;
use Andreo\OAuthApiConnectorBundle\Middleware\GetAccessTokenMiddleware;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareAggregate;
use Andreo\OAuthApiConnectorBundle\Middleware\StoreAccessTokenAsSessionAttributeMiddleware;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
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
            foreach ($options['clients'] ?? [] as $clientName => $config) {
                $api = $config['api'];
                $version = $config['version'] ?? 'default';

                $zoneConfigs = [];
                foreach ($config['zones'] ?? [] as $id => $zoneConfig) {
                    $zoneConfigs[] =  [
                        'zone_id' => $id,
                        'successful_response_uri' => $zoneConfig['successful_response_uri']
                    ];
                }
                $attributeDef = (new Definition(AttributeBag::class, [[
                    'client_id' => $config['credentials']['id'],
                    'client_name' => $clientName,
                    'client_secret' => $config['credentials']['secret'],
                    'callback_uri' => 'andreo.oauth.client.authentication',
                    'auth_uri' => $api['auth_uri'],
                    'zones' => $zoneConfigs
                ]]))
                ->setPublic(false)
                ->setFactory([AttributeBag::class, 'fromConfig']);

                $clientMiddlewares = [];

                $getAccessTokenMiddlewarePerClientDef = (new Definition(GetAccessTokenMiddleware::class, [
                    new Reference("andreo.oauth.http_client.$type.$version")
                ]))
                ->setPublic(false);

                $container->setDefinition(
                    $getAccessTokenMiddlewarePerClientId = "andreo.oauth_client.middleware.get_access_token.$clientName",
                    $getAccessTokenMiddlewarePerClientDef
                );

                $clientMiddlewares[] = [new Reference($getAccessTokenMiddlewarePerClientId), 1500];

                $accessTokenStorage = $config['access_token']['storage'];
                foreach ($accessTokenStorage['middleware'] as [$id, $priority]) {
                    if (!$container->hasDefinition($id)) {
                        $container->register($id);
                    }
                    $clientMiddlewares[] = [new Reference($id), $priority];
                }

                $middlewareAggregatePerClientDef = (new Definition(MiddlewareAggregate::class, [
                    new Reference("andreo.oauth_client.middleware_aggregate.$type")
                ]))
                ->setPublic(false)
                ->addMethodCall('merge', [$clientMiddlewares]);

                $container->setDefinition(
                    $middlewareAggregatePerClientId = "andreo.oauth_client.middleware_aggregate.$type.$clientName",
                    $middlewareAggregatePerClientDef
                );

                $clientDef = (new Definition(Client::class, [
                    $attributeDef,
                    new Reference($middlewareAggregatePerClientId),
                    new Reference('router')
                ]))
                ->setPublic(false)
                ->addTag('andreo.oauth_client.client', [
                    'name' => $clientName,
                    'type' => $type,
                    'api' => $api['version'],
                ]);

                $container->setDefinition("andreo.oauth_client.client.$type.$clientName", $clientDef);
            }
        }
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
            foreach ($options['clients'] ?? [] as $clientName => $config) {
                $api = $config['api'];
                $version = $config['version'] ?? 'default';

                $httpClientConfigProviderDef = (new Definition(ConfigProviderInterface::class))
                    ->setPublic(false)
                    ->setFactory([HttpClientConfigProvider::class, 'fromConfig'])
                    ->addArgument($api);

                $httpClientDef = (new Definition($config['http_client_id']))
                    ->setPublic(false);

                $container->setDefinition($apiConfigProvider = "andreo.oauth_client.http_config_provider.$clientName", $httpClientConfigProviderDef);
                $container->setDefinition($httpClientId = "andreo.oauth.http_client.$type.$version", $httpClientDef);

                $guzzleClientConfigs['clients'][$clientName] = [
                    'config_provider_id' => $apiConfigProvider,
                    'decorator_id' => $httpClientId
                ];
            }
        }

        $container->prependExtensionConfig('andreo_guzzle', $guzzleClientConfigs);
    }
}
