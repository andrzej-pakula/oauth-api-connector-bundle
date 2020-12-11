<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

final class ClientId implements HttpParameterInterface
{
    private const KEY_ID = 'client_id';

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param array<string, string|int> $httpParams
     * @return array<string, string|int>
     */
    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY_ID] = $this->getId();

        return $httpParams;
    }
}
