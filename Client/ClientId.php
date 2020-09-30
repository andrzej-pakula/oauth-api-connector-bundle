<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


final class ClientId
{
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

    public function mapRequestParams(array $requestParams): array
    {
        $requestParams['client_id'] = $this->getId();

        return $requestParams;
    }
}
