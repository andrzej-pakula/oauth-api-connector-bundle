<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection;


use Andreo\OAuthClientBundle\ClientType\GitHub\Client\AllowSignup;
use Andreo\OAuthClientBundle\ClientType\GitHub\Client\Login;
use Andreo\OAuthClientBundle\DependencyInjection\TypeExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class Extension implements TypeExtensionInterface
{
    public function getAuthorizationUriDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition
    {
        $credentials = $config['credentials'];

        $allowSignupDef = (new Definition(AllowSignup::class, [$credentials['allow_signup']]))
            ->setPrivate(true);
        $loginDef = (new Definition(Login::class, [$credentials['login']]))
            ->setPrivate(true);

        $baseDef->setMethodCalls([
            ['addHttpParameter', [$allowSignupDef], true],
            ['addHttpParameter', [$loginDef], true],
        ])
        ->setPrivate(true);

        return $baseDef;
    }

    public function getAccessTokenQueryDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition
    {
        return $baseDef;
    }

    public function getMiddlewareDefs(ContainerBuilder $container, array $config, array &$baseMiddlewares): iterable
    {
        return $baseMiddlewares;
    }
}
