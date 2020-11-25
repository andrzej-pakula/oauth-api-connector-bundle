<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\AuthorizationUri;


use Andreo\OAuthClientBundle\Client\AggregateHTTPParamInterface;

final class Scope implements AggregateHTTPParamInterface
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

    public function aggregateParam(array $httpParams = []): array
    {
        $httpParams[self::KEY] = (string)$this;

        return $httpParams;
    }

    public function __toString(): string
    {
        return implode(',', $this->parts);
    }
}
