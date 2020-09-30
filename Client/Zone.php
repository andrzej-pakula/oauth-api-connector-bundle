<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


final class Zone
{
    private ZoneId $id;

    private string $successfulResponseURI;

    public function __construct(ZoneId $id, string $successfulResponseURI)
    {
        $this->id = $id;
        $this->successfulResponseURI = $successfulResponseURI;
    }

    public function getId(): ZoneId
    {
        return $this->id;
    }

    public function getSuccessfulResponseUrl(): string
    {
        return $this->successfulResponseURI;
    }
}
