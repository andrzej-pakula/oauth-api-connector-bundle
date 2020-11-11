<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

final class ZoneId
{
    private const KEY = 'zone';

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
            throw new RuntimeException('Missing zone parameter.');
        }

        $id = $request->attributes->get(self::KEY);

        return new self($id);
    }

    public static function inRequest(Request $request): bool
    {
        return $request->attributes->has(self::KEY);
    }

    public function mapRequestParams(array $requestParams = []): array
    {
        $requestParams[self::KEY] = $this->getId();

        return $requestParams;
    }
}
