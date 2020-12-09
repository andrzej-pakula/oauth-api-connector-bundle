<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection;


use Andreo\OAuthClientBundle\Client\AuthorizationUri\Scope;
use Andreo\OAuthClientBundle\ClientType\GitHub\Versions;
use Andreo\OAuthClientBundle\ClientType\GitHub\Http\OAuthHTTPClient;
use Andreo\OAuthClientBundle\DependencyInjection\SupportedType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition(SupportedType::GITHUB);

        $node
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
                                    ->scalarNode('login')->defaultNull()->end()
                                    ->booleanNode('allow_signup')->defaultTrue()->end()
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
                                    ->enumNode('storage')->values(['session', 'cookie'])->defaultValue('session')->end()
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
            ->end();

        return $node;
    }

    private function getApiNode(): ArrayNodeDefinition
    {
        $apiConfigGen = static function (string $version) {
            return [
                'version' => $version,
                'api_uri' => "https://api.github.com",
                'auth_uri' => "https://github.com/login/oauth/authorize"
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
}
