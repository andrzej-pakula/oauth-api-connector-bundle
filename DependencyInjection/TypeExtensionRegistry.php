<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\DependencyInjection;


use Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection\Extension as FacebookExtension;
use Andreo\OAuthClientBundle\ClientType\Github\DependencyInjection\Extension as GithubExtension;

final class TypeExtensionRegistry
{
    private const REGISTRY = [
        SupportedType::FACEBOOK => FacebookExtension::class,
        SupportedType::GITHUB => GithubExtension::class
    ];

    public static function isSupported(string $type): bool
    {
        return in_array($type, SupportedType::ALL);
    }

    public static function has(string $type): bool
    {
        return class_exists(self::REGISTRY[$type]);
    }

    public static function get(string $type): TypeExtensionInterface
    {
        $type = self::REGISTRY[$type];

        return new $type();
    }
}
