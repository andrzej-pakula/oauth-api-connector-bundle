<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Util;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\ClientId;

final class StoreKeyGenerator
{
    public static function generate(ClientId $clientId, string $key): string
    {
        return $clientId->getId() . '_'  . $key;
    }
}
