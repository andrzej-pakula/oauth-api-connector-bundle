<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection;


use Andreo\OAuthClientBundle\ClientType\GitHub\Http\OAuthHTTPClient;
use Andreo\OAuthClientBundle\ClientType\SupportedType;
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
                ->arrayNode(SupportedType::GITHUB)
                    ->children()
                        ->arrayNode('clients')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('http_client_id')->defaultValue(OAuthHTTPClient::class)->cannotBeEmpty()->end()
                                        ->arrayNode('auth')
                                            ->children()
                                                ->scalarNode('id')->end()
                                                ->scalarNode('secret')->end()
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
                ->end()
            ->end();

        return $this->rootNode;
    }
}
