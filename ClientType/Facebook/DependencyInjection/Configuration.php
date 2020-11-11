<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection;


use Andreo\OAuthClientBundle\Client\RequestContext\Scope;
use Andreo\OAuthClientBundle\ClientType\Facebook\Http\OAuthHTTPClient;
use Andreo\OAuthClientBundle\ClientType\Facebook\Versions;
use Andreo\OAuthClientBundle\ClientType\SupportedType;
use Andreo\OAuthClientBundle\Middleware\CheckCookieAccessTokenMiddleware;
use Andreo\OAuthClientBundle\Middleware\CheckSessionAccessTokenMiddleware;
use Andreo\OAuthClientBundle\Middleware\StoreAccessTokenAsCookieMiddleware;
use Andreo\OAuthClientBundle\Middleware\StoreAccessTokenAsSessionAttributeMiddleware;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var ArrayNodeDefinition&NodeParentInterface
     */
    private NodeParentInterface $rootNode;

    public function __construct(NodeParentInterface $rootNode)
    {
        $this->rootNode = $rootNode;
    }

    public function getConfigTreeBuilder()
    {
        $this->rootNode
            ->children()
                ->arrayNode(SupportedType::FACEBOOK)
                    ->children()
                        ->arrayNode('clients')
                            ->arrayPrototype()
                                ->children()
                                    ->append($this->getApiNode())
                                    ->scalarNode('http_client_id')->defaultValue(OAuthHTTPClient::class)->cannotBeEmpty()->end()
                                    ->arrayNode('credentials')
                                        ->children()
                                            ->scalarNode('id')->cannotBeEmpty()->end()
                                            ->scalarNode('secret')->cannotBeEmpty()->end()
                                            ->arrayNode('scope')
                                                ->beforeNormalization()
                                                    ->ifString()
                                                    ->then(static function(string $scope) {
                                                        return Scope::fromString($scope)->getParts();
                                                    })
                                                ->end()
                                                ->scalarPrototype()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('access_token')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->booleanNode('long_live')->defaultFalse()->end()
                                            ->append($this->getStorageNode())
                                        ->end()
                                    ->end()
                                    ->arrayNode('zones')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('successful_response_uri')->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $this->rootNode;
    }

    private function getApiNode(): ArrayNodeDefinition
    {
        $apiConfigGen = static function (string $version) {
            return [
                'version' => $version,
                'api_uri' => "https://graph.facebook.com/$version",
                'auth_uri' => "https://www.facebook.com/$version/dialog/oauth"
            ];
        };

        $apiConfigLatest = $apiConfigGen(Versions::LATEST);

        $node = new ArrayNodeDefinition('api');
        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifInArray(Versions::ALL)
                ->then($apiConfigGen)
            ->end()
            ->children()
                ->scalarNode('version')->cannotBeEmpty()->defaultValue($apiConfigLatest['version'])->end()
                ->scalarNode('api_uri')->cannotBeEmpty()->defaultValue($apiConfigLatest['api_uri'])->end()
                ->scalarNode('auth_uri')->cannotBeEmpty()->defaultValue($apiConfigLatest['auth_uri'])->end()
            ->end()
        ->end();

        return $node;
    }

    private function getStorageNode(): ArrayNodeDefinition
    {
        $storageMiddlewareMap = [
            'session' => [
                [CheckSessionAccessTokenMiddleware::class, 3500],
                [StoreAccessTokenAsSessionAttributeMiddleware::class, 1250]
            ],
            'cookie' => [
                [CheckCookieAccessTokenMiddleware::class, 3500],
                [StoreAccessTokenAsCookieMiddleware::class, 1250]
            ]
        ];
        $supportedStorages = array_keys($storageMiddlewareMap);

        $node = new ArrayNodeDefinition('storage');
        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifNotInArray($supportedStorages)
                ->thenInvalid('Unsupported storage. Available: ' . implode(', ', $supportedStorages))
            ->end()
            ->beforeNormalization()
                ->ifInArray($supportedStorages)
                ->then(static function (string $version) use ($storageMiddlewareMap) {
                   return ['middleware' => $storageMiddlewareMap[$version]];
                })
            ->end()
            ->children()
                ->variableNode('middleware')
                    ->defaultValue($storageMiddlewareMap['session'])
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !is_array($v);
                        })
                        ->thenInvalid('Middleware option must be array of arrays with listed middleware id and priority: - [string id, int priority],')
                    ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }
}
