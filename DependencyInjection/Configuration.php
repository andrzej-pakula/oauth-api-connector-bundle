<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection\Configuration as FacebookConfiguration;
use Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection\Configuration as GithubConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('andreo_o_auth_client');
        $rootNode = $treeBuilder->getRootNode();

        $this->addTypeSections($rootNode);
        $this->validateTypes($rootNode);
        $this->validateClients($rootNode);

        return $treeBuilder;
    }

    private function addTypeSections(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->append((new FacebookConfiguration())->getConfigTreeBuilder())
            ->append((new GithubConfiguration())->getConfigTreeBuilder());
    }

    private function validateTypes(NodeDefinition $rootNode): void
    {
        $rootNode
            ->beforeNormalization()
            ->ifTrue(function (array $configs) {
                foreach (array_keys($configs) as $type) {
                    if (!TypeExtensionRegistry::isSupported($type)) {
                        throw new LogicException(ucfirst($type).' type is not supported.');
                    }
                    if (!TypeExtensionRegistry::has($type)) {
                        throw new LogicException(ucfirst($type)." type support cannot be enabled as the component is not installed. Try running 'composer require andreo/$type-oauth'.");
                    }
                }
            })
            ->thenEmptyArray()
            ->end();
    }

    private function validateClients(NodeDefinition $rootNode): void
    {
        $rootNode
            ->validate()
                ->ifTrue(function (array $configs) {
                    if (1 === count($configs)) {
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
    }
}
