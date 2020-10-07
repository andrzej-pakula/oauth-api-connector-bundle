<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


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

    public static function fromRequest(Request $request): ?self
    {
        $id = $request->attributes->get(self::KEY);
        if (null === $id) {
            return null;
        }

        return new self($id);
    }

    public function mapRequestParams(array $requestParams = []): array
    {
        $requestParams[self::KEY] = $this->getId();

        return $requestParams;
    }
}
