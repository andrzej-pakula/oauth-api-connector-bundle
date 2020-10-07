<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


interface ClientFactoryInterface
{
    public function __invoke(array $options, iterable $middleware): ClientInterface;
}
