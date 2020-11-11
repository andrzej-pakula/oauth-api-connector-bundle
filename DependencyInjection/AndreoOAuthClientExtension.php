<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Andreo\OAuthClientBundle\Client\Client;
use Andreo\OAuthClientBundle\ClientType\Facebook\Middleware\ExchangeAccessTokenMiddleware;
use Andreo\OAuthClientBundle\ClientType\SupportedType;
use Andreo\OAuthClientBundle\Http\Provider\HttpClientConfigProvider;
use Andreo\OAuthClientBundle\Middleware\GetAccessTokenMiddleware;
use Andreo\OAuthClientBundle\Middleware\MiddlewareAggregate;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class AndreoOAuthClientExtension extends Extension implements PrependExtensionInterface
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
                $credentials = $config['credentials'];

                $zoneConfigs = [];
                foreach ($config['zones'] ?? [] as $id => $zoneConfig) {
                    $zoneConfigs[] =  [
                        'zone_id' => $id,
                        'successful_response_uri' => $zoneConfig['successful_response_uri']
                    ];
                }
                $attributeDef = (new Definition(Context::class, [[
                    'client_id' => $credentials['id'],
                    'client_secret' => $credentials['secret'],
                    'scope' => $credentials['scope'],
                    'client_name' => $clientName,
                    'callback_uri' => 'andreo.oauth.client.authentication',
                    'auth_uri' => $api['auth_uri'],
                    'zones' => $zoneConfigs
                ]]))
                ->setPublic(false)
                ->setFactory([Context::class, 'fromConfig']);

                $clientMiddleware = [];

                $getAccessTokenMiddlewarePerClientDef = (new Definition(GetAccessTokenMiddleware::class, [
                    new Reference("andreo.oauth.http_client.$type.$version")
                ]))
                ->setPublic(false);

                $container->setDefinition(
                    $getAccessTokenMiddlewarePerClientId = "andreo.oauth_client.middleware.get_access_token.$clientName",
                    $getAccessTokenMiddlewarePerClientDef
                );

                $clientMiddleware[] = [new Reference($getAccessTokenMiddlewarePerClientId), 1500];

                $accessTokenConfig = $config['access_token'];

                if ($type === SupportedType::FACEBOOK && $accessTokenConfig['long_live']) {
                    $exchangeAccessTokenMiddlewarePerClientDef = (new Definition(ExchangeAccessTokenMiddleware::class, [
                        new Reference("andreo.oauth.http_client.$type.$version")
                    ]))
                    ->setPublic(false);

                    $container->setDefinition(
                        $exchangeAccessTokenMiddlewarePerClientId = "andreo.oauth_client.middleware.exchange_access_token.$clientName",
                        $exchangeAccessTokenMiddlewarePerClientDef
                    );

                    $clientMiddleware[] = [new Reference($exchangeAccessTokenMiddlewarePerClientId), 1300];
                }

                $accessTokenStorage = $accessTokenConfig['storage'];
                foreach ($accessTokenStorage['middleware'] as [$id, $priority]) {
                    if (!$container->hasDefinition($id)) {
                        $container->register($id);
                    }

                    $clientMiddleware[] = [new Reference($id), $priority];
                }

                $middlewareAggregatePerClientDef = (new Definition(MiddlewareAggregate::class, [
                    new Reference("andreo.oauth_client.middleware_aggregate.$type")
                ]))
                ->setPublic(false)
                ->addMethodCall('merge', [$clientMiddleware]);

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
