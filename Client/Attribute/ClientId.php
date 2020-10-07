<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


final class ClientId
{
    private const KEY_ID = 'client_id';
    public const KEY_NAME = 'client_name';

    private string $id;

    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function mapRequestParams(array $requestParams, ?string $key = null): array
    {
        $requestParams[$key ?? self::KEY_ID] = $this->getId();

        return $requestParams;
    }
}
