<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Util;

use Andreo\OAuthClientBundle\Client\ClientName;

final class KeyGenerator
{
    public static function get(ClientName $clientName, string $key): string
    {
        return base64_encode($clientName->getName().$key);
    }
}
