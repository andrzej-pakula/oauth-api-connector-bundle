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

    /**
     * @return self[]
     */
    public static function createRegistryByConfig(array $config): array
    {
        $zones = [];
        foreach ($config['zones'] as $zone) {
            $zones[$zone['zone_id']] = new self(
                new ZoneId($zone['zone_id']),
                $zone['successful_response_uri']
            );
        }

        return $zones;
    }
}
