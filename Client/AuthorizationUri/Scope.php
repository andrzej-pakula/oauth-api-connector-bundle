<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AuthorizationUri;

use Andreo\OAuthClientBundle\Client\HttpParameterInterface;

final class Scope implements HttpParameterInterface
{
    private const KEY = 'scope';

    /**
     * @var string[]
     */
    private array $parts;

    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public static function fromString(string $parts): self
    {
        $parts = array_map(static fn ($part) => trim($part), explode(',', $parts));

        return new self($parts);
    }

    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY] = (string) $this;

        return $httpParams;
    }

    public function __toString(): string
    {
        return implode(',', $this->parts);
    }
}
