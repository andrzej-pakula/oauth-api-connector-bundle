<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


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

    public static function fromConfig(array $options): self
    {
        return new self(
            new ZoneId($options['zone_id']),
            $options['successful_response_uri']
        );
    }
}
