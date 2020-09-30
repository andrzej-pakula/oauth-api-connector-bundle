<?php /** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
    }
}
