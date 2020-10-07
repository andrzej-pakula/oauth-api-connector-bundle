<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ClientType\Facebook\Provider;


use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderInterface;

final class V8MetaDataProvider implements MetaDataProviderInterface
{
    use MetaDataProviderTrait;

    public static function getVersion(): string
    {
        return 'v8.0';
    }
}
