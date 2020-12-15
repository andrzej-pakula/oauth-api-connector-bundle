<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection;

use Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken\GetAccessToken;
use Andreo\OAuthClientBundle\ClientType\Facebook\Middleware\RefreshAccessTokenMiddleware;
use Andreo\OAuthClientBundle\DependencyInjection\TypeExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class Extension implements TypeExtensionInterface
{
    public function getAuthorizationUriDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition
    {
        return $baseDef;
    }

    public function getAccessTokenQueryDef(ContainerBuilder $container, array $config, Definition $baseDef): Definition
    {
        return new Definition(GetAccessToken::class, $baseDef->getArguments());
    }

    public function getMiddlewareDefs(ContainerBuilder $container, array $config, array $baseMiddlewares): array
    {
        $accessTokenConfig = $config['access_token'];
        $clientName = $config['client_name'];

        if ($accessTokenConfig['long_live']) {
            $refreshAccessTokenMiddlewarePerClientDef = (new Definition(RefreshAccessTokenMiddleware::class, [
                new Reference("andreo.oauth.http_client.$clientName"),
            ]))
            ->setPublic(false);

            $container->setDefinition(
                $refreshAccessTokenMiddlewarePerClientId = "andreo.oauth_client.middleware.refresh_access_token.$clientName",
                $refreshAccessTokenMiddlewarePerClientDef
            );

            $baseMiddlewares[] = [new Reference($refreshAccessTokenMiddlewarePerClientId), 1300];
        }

        return $baseMiddlewares;
    }
}
