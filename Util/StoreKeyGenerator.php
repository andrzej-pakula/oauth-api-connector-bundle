<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Util;


use Andreo\OAuthClientBundle\Client\RequestContext\ClientId;

final class StoreKeyGenerator
{
    public static function generate(ClientId $clientId, string $key): string
    {
        return base64_encode($clientId->getName() . $key);
    }
}
