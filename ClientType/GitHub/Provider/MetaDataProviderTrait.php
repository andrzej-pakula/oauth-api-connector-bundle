<?php


namespace Andreo\OAuthApiConnectorBundle\ClientType\GitHub\Provider;


use Andreo\OAuthApiConnectorBundle\ClientType\SupportedType;

trait MetaDataProviderTrait
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
