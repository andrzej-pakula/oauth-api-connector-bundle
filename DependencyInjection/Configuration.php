<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection\Configuration as FacebookConfiguration;
use Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection\Configuration as GithubConfiguration;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('andreo_o_auth_client');
        $rootNode = $treeBuilder->getRootNode();

        $this->addFacebookSection($rootNode);
        $this->addGithubSection($rootNode);

        $rootNode
            ->validate()
                ->ifTrue(function (array $configs) {
                    if (count($configs) === 1) {
                        return false;
                    }

                    $clientNames = [];
                    foreach ($configs as $key => $config) {
                        $clientNames[] = array_keys($config['clients']);
                    }

                    $clientNames = array_merge(...$clientNames);
                    $originCount = count($clientNames);
                    $uniqueClientNames = array_unique($clientNames, SORT_REGULAR);

                    return $originCount !== count($uniqueClientNames);
                })
                ->thenInvalid('Client names must be unique.')
            ->end();

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
