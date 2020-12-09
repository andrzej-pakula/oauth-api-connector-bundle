<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RedirectUri;


use Andreo\OAuthClientBundle\Client\HttpParameterInterface;
use Andreo\OAuthClientBundle\Exception\MissingZoneException;
use Symfony\Component\HttpFoundation\Request;

final class ZoneId implements HttpParameterInterface
{
    public const KEY = 'zone';

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public static function fromRequest(Request $request): self
    {
        if (!self::inRequest($request)) {
            throw new MissingZoneException();
        }

        $id = $request->attributes->get(self::KEY);

        return new self($id);
    }

    public static function inRequest(Request $request): bool
    {
        return $request->attributes->has(self::KEY);
    }

    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->getId();

        return $httpParams;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
