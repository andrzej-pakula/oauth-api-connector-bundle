<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\Provider;


use Andreo\OAuthClientBundle\Client\MetaDataProviderInterface;
use Andreo\OAuthClientBundle\ClientType\SupportedType;

final class MetaDataProvider implements MetaDataProviderInterface
{
    public static function getApiUri(): string
    {
        return 'https://github.com/login';
    }

    public static function getAuthorizationUri(): string
    {
        return 'https://github.com/login/oauth/authorize';
    }

    public static function getType(): string
    {
        return SupportedType::GITHUB;
    }

    public static function getVersion(): ?string
    {
        return null;
    }
}
