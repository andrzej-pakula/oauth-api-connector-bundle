<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;

interface ClientFactoryInterface
{
    public function __invoke(Attributes $attributes, iterable $middleware): ClientInterface;
}
