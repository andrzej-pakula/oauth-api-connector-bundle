<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;

interface ClientFactoryInterface
{
    public function __invoke(AttributeBag $attributes, iterable $middleware): ClientInterface;
}
