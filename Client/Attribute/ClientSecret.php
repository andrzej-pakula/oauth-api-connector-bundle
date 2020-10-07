<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


final class ClientSecret
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
