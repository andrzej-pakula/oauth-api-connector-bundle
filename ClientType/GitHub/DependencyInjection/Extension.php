<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection;

use Andreo\OAuthClientBundle\ClientType\GitHub\AccessToken\AccessTokenProvider;
use Andreo\OAuthClientBundle\ClientType\GitHub\Client\AllowSignup;
use Andreo\OAuthClientBundle\ClientType\GitHub\Client\Login;
use Andreo\OAuthClientBundle\DependencyInjection\TypeExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class Extension implements TypeExtensionInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function getAccessTokenProviderDef(ContainerBuilder $container, array $config): Definition
    {
        return new Definition(AccessTokenProvider::class, [
            new Reference("andreo.oauth.http_client.{$config['client_name']}"),
        ]);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function getAuthorizationUriDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition
    {
        $credentials = $config['credentials'];

        $allowSignupDef = (new Definition(AllowSignup::class, [$credentials['allow_signup']]))
            ->setPublic(false);

        $loginDef = (new Definition(Login::class, [$credentials['login']]))
            ->setPublic(false);

        $baseDef
            ->addMethodCall('addHttpParameter', [$allowSignupDef], true)
            ->addMethodCall('addHttpParameter', [$loginDef], true, );

        return $baseDef;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function getMiddlewareDefs(ContainerBuilder $container, array $config, array $baseMiddlewares): array
    {
        return $baseMiddlewares;
    }
}
