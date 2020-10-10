<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;

use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Middleware\MiddlewareInterface;

final class ClientFactory implements ClientFactoryInterface
{
    /**
     * @param iterable<MiddlewareInterface>
     */
    public function __invoke(AttributeBag $attributes, iterable $middleware): ClientInterface
    {
        return new Client($middleware, $attributes);
    }
}
