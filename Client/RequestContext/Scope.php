<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


final class Scope
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
        $parts = array_map(static function($part) {
            return trim($part);
        }, explode(',', $parts));

        return new self($parts);
    }

    public function mapQuery(array $requestParams = []): array
    {
        $requestParams[self::KEY] = (string)$this;

        return $requestParams;
    }

    public function __toString(): string
    {
        return implode(',', $this->parts);
    }
}
