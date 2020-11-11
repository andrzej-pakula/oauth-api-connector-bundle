<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


final class ClientId
{
    private const KEY_ID = 'client_id';
    private const KEY_NAME = 'client_name';

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

    public function mapQueryId(array $requestParams): array
    {
        $requestParams[self::KEY_ID] = $this->getId();

        return $requestParams;
    }

    public function mapQueryName(array $requestParams): array
    {
        $requestParams[self::KEY_NAME] = $this->getName();

        return $requestParams;
    }
}
