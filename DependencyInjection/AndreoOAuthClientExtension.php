<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Andreo\GuzzleBundle\Configurator\ConfigProviderInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessToken;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\AuthorizationUri;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\Scope;
use Andreo\OAuthClientBundle\Client\Client;
use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\ClientId;
use Andreo\OAuthClientBundle\Client\ClientName;
use Andreo\OAuthClientBundle\Client\ClientSecret;
use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;
use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;
use Andreo\OAuthClientBundle\Client\Zone;
use Andreo\OAuthClientBundle\Http\Provider\HttpClientConfigProvider;
use Andreo\OAuthClientBundle\Middleware\GetAccessTokenMiddleware;
use Andreo\OAuthClientBundle\Middleware\MiddlewareAggregate;
use Andreo\OAuthClientBundle\Middleware\StoreAccessTokenMiddleware;
use Andreo\OAuthClientBundle\Middleware\TryRestoreAccessTokenMiddleware;
use Andreo\OAuthClientBundle\Storage\CookieStorage;
use Andreo\OAuthClientBundle\Storage\Encoder\Encoder;
use Andreo\OAuthClientBundle\Storage\Serializer\SerializerInterface;
use Andreo\OAuthClientBundle\Storage\SessionStorage;
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

                $httpClientConfigProviderDef = (new Definition(ConfigProviderInterface::class))
                    ->setPublic(false)
                    ->setFactory([HttpClientConfigProvider::class, 'fromConfig'])
                    ->addArgument($api);

                $httpClientDef = (new Definition($config['http_client_id']))
                    ->setPublic(false);

                $container->setDefinition($apiConfigProvider = "andreo.oauth_client.http_config_provider.$clientName", $httpClientConfigProviderDef);
                $container->setDefinition($httpClientId = "andreo.oauth.http_client.$clientName", $httpClientDef);

                $guzzleClientConfigs['clients'][$clientName] = [
                    'config_provider_id' => $apiConfigProvider,
                    'decorator_id' => $httpClientId,
                ];
            }
        }

        $container->prependExtensionConfig('andreo_guzzle', $guzzleClientConfigs);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        foreach ($config as $type => $options) {
            $typeExtension = TypeExtensionRegistry::get($type);

            foreach ($options['clients'] ?? [] as $clientName => $config) {
                $credentials = $config['credentials'];
                $configForTypeExtension = array_merge($config, ['client_name' => $clientName]);
                $api = $config['api'];

                $clientIdDef = (new Definition(ClientId::class, [$credentials['id']]))
                    ->setPublic(false);
                $clientSecretDef = (new Definition(ClientSecret::class, [$credentials['secret']]))
                    ->setPublic(false);
                $clientNameDef = (new Definition(ClientName::class, [$clientName]))
                    ->setPublic(false);
                $scopeDef = (new Definition(Scope::class, [$credentials['scope']]))
                    ->setPublic(false);

                $authorizationUriDef = (new Definition(AuthorizationUri::class, [
                    $api['auth_uri'],
                ]))
                ->setMethodCalls([
                    ['addHttpParameter', [$clientIdDef], true],
                    ['addHttpParameter', [$scopeDef], true],
                ])
                ->setPublic(false);
                $authorizationUriDef = $typeExtension->getAuthorizationUriDef($container, $configForTypeExtension, $authorizationUriDef);

                $redirectUriDef = (new Definition(RedirectUri::class, [
                    'andreo.oauth.authorization',
                ]))
                ->addMethodCall('addHttpParameter', [$clientNameDef], true)
                ->setPublic(false);

                $zoneRegistry = [];
                foreach ($config['zones'] ?? [] as $id => $zoneConfig) {
                    $zoneRegistry[$id] = (new Definition(Zone::class, [
                        (new Definition(ZoneId::class, [$id]))->setPublic(false),
                        $zoneConfig['successful_response_uri'],
                    ]))->setPublic(false);
                }

                $clientContextDef = (new Definition(ClientContext::class, [
                    $clientIdDef,
                    $clientSecretDef,
                    $clientNameDef,
                    $redirectUriDef,
                    $authorizationUriDef,
                    $zoneRegistry,
                ]))
                ->setPublic(false);

                $clientMiddleware = [];

                $accessTokenQueryDef = (new Definition(GetAccessToken::class, [
                    $credentials['id'],
                    $credentials['secret'],
                ]))
                ->setPublic(false);
                $accessTokenQueryDef = $typeExtension->getAccessTokenQueryDef($container, $configForTypeExtension, $accessTokenQueryDef);

                $getAccessTokenMiddlewarePerClientDef = (new Definition(GetAccessTokenMiddleware::class, [
                    new Reference("andreo.oauth.http_client.$clientName"),
                    $accessTokenQueryDef,
                ]))
                ->setPublic(false);

                $container->setDefinition(
                    $getAccessTokenMiddlewarePerClientId = "andreo.oauth_client.middleware.get_access_token.$clientName",
                    $getAccessTokenMiddlewarePerClientDef
                );

                $clientMiddleware[] = [new Reference($getAccessTokenMiddlewarePerClientId), 1500];

                $accessTokenConfig = $config['access_token'];
                $accessTokenStorage = $accessTokenConfig['storage'];

                $storageResolver = [
                    'session' => fn () => new Reference(SessionStorage::class),
                    'cookie' => static function () use ($container) {
                        if (!$container->has(CookieStorage::class)) {
                            $container->register(CookieStorage::class)
                                ->setArguments([
                                    new Reference(Encoder::class),
                                    new Reference(SerializerInterface::class),
                                ]);
                        }

                        return new Reference(CookieStorage::class);
                    },
                ];

                $tryRestoreMiddlewareDef = new Definition(TryRestoreAccessTokenMiddleware::class, [
                    $storageResolver[$accessTokenStorage](),
                ]);
                $container->setDefinition(
                    $tryRestoreMiddlewareId = "andreo.oauth_client.middleware.try_restore_access_token.$clientName",
                    $tryRestoreMiddlewareDef
                );
                $clientMiddleware[] = [new Reference($tryRestoreMiddlewareId), 3500];

                $storeMiddlewareDef = new Definition(StoreAccessTokenMiddleware::class, [
                    $storageResolver[$accessTokenStorage](),
                ]);
                $container->setDefinition(
                    $storeMiddlewareId = "andreo.oauth_client.middleware.store_access_token.$clientName",
                    $storeMiddlewareDef
                );
                $clientMiddleware[] = [new Reference($storeMiddlewareId), 1250];

                $clientMiddleware = $typeExtension->getMiddlewareDefs($container, $configForTypeExtension, $clientMiddleware);

                $middlewareAggregatePerClientDef = (new Definition(MiddlewareAggregate::class, [
                    new Reference("andreo.oauth_client.middleware_aggregate.$type"),
                ]))
                ->setPublic(false)
                ->addMethodCall('merge', [$clientMiddleware]);

                $container->setDefinition(
                    $middlewareAggregatePerClientId = "andreo.oauth_client.middleware_aggregate.$clientName",
                    $middlewareAggregatePerClientDef
                );

                $clientDef = (new Definition(Client::class, [
                    new Reference($middlewareAggregatePerClientId),
                    $clientContextDef,
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
}
