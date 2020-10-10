<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Andreo\OAuthApiConnectorBundle\ClientType\Facebook\DependencyInjection\Configuration as FacebookConfiguration;
use Andreo\OAuthApiConnectorBundle\ClientType\GitHub\DependencyInjection\Configuration as GithubConfiguration;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('andreo_o_auth_api_connector');
        $rootNode = $treeBuilder->getRootNode();

        $this->addFacebookSection($rootNode);
        $this->addGithubSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition&NodeParentInterface
     */
    private function addFacebookSection(ArrayNodeDefinition $rootNode): NodeParentInterface
    {
        if (!class_exists(FacebookConfiguration::class)) {
            return $rootNode;
        }

        $facebookConfiguration = new FacebookConfiguration($rootNode);

        return $facebookConfiguration->getConfigTreeBuilder();
    }

    /**
     * @return ArrayNodeDefinition&NodeParentInterface
     */
    private function addGithubSection(ArrayNodeDefinition $rootNode): NodeParentInterface
    {
        if (!class_exists(GithubConfiguration::class)) {
            return $rootNode;
        }

        $gitHubConfiguration = new GithubConfiguration($rootNode);

        return $gitHubConfiguration->getConfigTreeBuilder();
    }
}
