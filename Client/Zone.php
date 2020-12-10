<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;

final class Zone
{
    private ZoneId $id;

    private string $successfulResponseUri;

    public function __construct(ZoneId $id, string $successfulResponseUri)
    {
        $this->id = $id;
        $this->successfulResponseUri = $successfulResponseUri;
    }

    public function getId(): ZoneId
    {
        return $this->id;
    }

    public function getSuccessfulResponseUri(): string
    {
        return $this->successfulResponseUri;
    }
}
