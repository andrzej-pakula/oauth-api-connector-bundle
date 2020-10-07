<?php


namespace Andreo\OAuthApiConnectorBundle\ClientType\Facebook\Provider;


use Andreo\OAuthApiConnectorBundle\ClientType\SupportedType;

trait MetaDataProviderTrait
{
    public static function getApiUri(): string
    {
        return 'https://graph.facebook.com/' . self::getVersion();
    }

    public static function getAuthorizationUri(): string
    {
        return 'https://www.facebook.com/' . self::getVersion() . '/dialog/oauth';
    }

    public static function getType(): string
    {
        return SupportedType::FACEBOOK;
    }

    public static function getVersion(): string
    {
        return '';
    }
}
