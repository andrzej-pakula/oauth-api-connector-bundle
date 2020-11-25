<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\AccessTokenQuery;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\AuthorizationUri;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\Scope;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\ClientId;
use Andreo\OAuthClientBundle\Client\Client;
use Andreo\OAuthClientBundle\Client\ClientName;
use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;
use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;
use Andreo\OAuthClientBundle\Client\Zone;
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

                $clientIdDef = (new Definition(ClientId::class, [$credentials['id']]))
                    ->setPrivate(true);
                $clientNameDef = (new Definition(ClientName::class, [$clientName]))
                    ->setPrivate(true);
                $scopeDef = (new Definition(Scope::class, [$credentials['scope']]))
                    ->setPrivate(true);

                $authorizationUriDef = (new Definition(AuthorizationUri::class, [
                    $api['auth_uri'],
                ]))
                ->setMethodCalls([
                    ['addHTTPParameter', [$clientIdDef], true],
                    ['addHTTPParameter', [$scopeDef], true],
                ])
                ->setPrivate(true);

                $redirectUriDef = (new Definition(RedirectUri::class, [
                    'andreo.oauth.authorization'
                ]))
                ->addMethodCall('addHTTPParameter', [$clientNameDef], true)
                ->setPrivate(true);

                $zoneRegistry = [];
                foreach ($config['zones'] ?? [] as $id => $zoneConfig) {
                    $zoneRegistry[$id] = (new Definition(Zone::class, [
                        (new Definition(ZoneId::class, [$id]))->setPrivate(true),
                        $zoneConfig['successful_response_uri']
                    ]))->setPrivate(true);
                }

                $clientContextDef = (new Definition(ClientContext::class, [
                    $clientNameDef,
                    $redirectUriDef,
                    $authorizationUriDef,
                    $zoneRegistry
                ]))
                ->setPrivate(true);

                $clientMiddleware = [];

                $accessTokenQueryDef = (new Definition(AccessTokenQuery::class, [
                    $credentials['id'],
                    $credentials['secret'],
                ]))
                ->setPrivate(true);

                $getAccessTokenMiddlewarePerClientDef = (new Definition(GetAccessTokenMiddleware::class, [
                    new Reference("andreo.oauth.http_client.$type.$version"),
                    $accessTokenQueryDef
                ]))
                ->setPrivate(true);

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
                    ->setPrivate(true);

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
                    new Reference($middlewareAggregatePerClientId),
                    $clientContextDef
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
            throw new RuntimeException('Andreo guzzle bundle is required in this bundle.');
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
