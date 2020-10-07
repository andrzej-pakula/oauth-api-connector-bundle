<?php

declare(strict_types=1);

namespace Andreo\OAuthApiConnectorBundle;


use Andreo\OAuthApiConnectorBundle\DependencyInjection\ClientFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AndreoOAuthApiConnectorBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ClientFactoryPass());
    }
}
