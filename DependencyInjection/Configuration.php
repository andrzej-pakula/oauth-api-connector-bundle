<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('andreo_oauth_api_connector');
        $rootNode = $treeBuilder->getRootNode();

        $clientsNode = new ArrayNodeDefinition('clients');

        $clientsNode
            ->children()
                ->arrayNode('facebook')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('client_id')->end()
                            ->scalarNode('client_secret')->end()
                            ->scalarNode('redirect_route')->end()
                            ->scalarNode('guzzle_client')->defaultValue(null)->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $rootNode
            ->children()
                ->append($clientsNode)
            ->end();

        return $treeBuilder;
    }
}
