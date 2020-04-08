<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Routing;

use Symfony\Component\Routing\Route;

class ClientRouteFactory
{
    public function create(string $clientName): Route
    {
        return new Route(
            '/connect',
            [
                '__controller' => 'Andreo\OAuthApiConnectorBundle\Security\ApiConnector::connect',
                'client_name' => $clientName
            ]
        );
    }
}