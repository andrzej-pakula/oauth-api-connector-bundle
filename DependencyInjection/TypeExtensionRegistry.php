<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\DependencyInjection;

use Andreo\OAuthClientBundle\ClientType\Facebook\DependencyInjection\Extension as FacebookExtension;
use Andreo\OAuthClientBundle\ClientType\GitHub\DependencyInjection\Extension as GitHubExtension;

final class TypeExtensionRegistry
{
    private const REGISTRY = [
        SupportedType::FACEBOOK => FacebookExtension::class,
        SupportedType::GITHUB => GitHubExtension::class,
    ];

    public static function isSupported(string $type): bool
    {
        return in_array($type, SupportedType::ALL, true);
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
