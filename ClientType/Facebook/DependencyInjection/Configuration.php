<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ClientType\Facebook\DependencyInjection;


use Andreo\OAuthApiConnectorBundle\ClientType\Facebook\Http\OAuthHTTPClient;
use Andreo\OAuthApiConnectorBundle\ClientType\Facebook\Provider\Versions;
use Andreo\OAuthApiConnectorBundle\ClientType\SupportedType;
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
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->enumNode('version')
                                            ->values(Versions::ALL)
                                            ->defaultValue(Versions::LATEST)
                                        ->end()
                                        ->scalarNode('http_client_id')->defaultValue(OAuthHTTPClient::class)->cannotBeEmpty()->end()
                                        ->arrayNode('auth')
                                            ->children()
                                                ->scalarNode('id')->cannotBeEmpty()->end()
                                                ->scalarNode('secret')->cannotBeEmpty()->end()
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
