<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection;


use Andreo\OAuthClientBundle\ClientType\Facebook\Middleware\ExchangeAccessTokenMiddleware;
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
        return $baseDef;
    }

    public function getMiddlewareDefs(ContainerBuilder $container, array $config, array &$baseMiddlewares): iterable
    {
        $accessTokenConfig = $config['access_token'];
        $clientName = $config['client_name'];

        if ($accessTokenConfig['long_live']) {
            $exchangeAccessTokenMiddlewarePerClientDef = (new Definition(ExchangeAccessTokenMiddleware::class, [
                new Reference("andreo.oauth.http_client.$clientName")
            ]))
            ->setPublic(false);

            $container->setDefinition(
                $exchangeAccessTokenMiddlewarePerClientId = "andreo.oauth_client.middleware.exchange_access_token.$clientName",
                $exchangeAccessTokenMiddlewarePerClientDef
            );

            $baseMiddlewares[] = [new Reference($exchangeAccessTokenMiddlewarePerClientId), 1300];
        }

        return $baseMiddlewares;
    }
}
