<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

final class ClientName implements HttpParameterInterface
{
    private const KEY = 'client';

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->getName();

        return $httpParams;
    }
}
