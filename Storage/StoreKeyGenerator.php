<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Storage;



use Andreo\OAuthClientBundle\Client\ClientName;

final class StoreKeyGenerator
{
    public static function generate(ClientName $clientName, string $key): string
    {
        return base64_encode($clientName->getName() . $key);
    }
}
