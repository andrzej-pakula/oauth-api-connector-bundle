<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Util;


use Andreo\OAuthApiConnectorBundle\Client\ClientId;

final class KeyGenerator
{
    public static function generate(ClientId $clientId, string $key): string
    {
        return $clientId->getId() . '_'  . $key;
    }
}
