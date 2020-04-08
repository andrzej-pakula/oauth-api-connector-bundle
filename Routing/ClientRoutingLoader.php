<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Routing;


use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Routing\RouteCollection;

class ClientRoutingLoader implements RouteLoaderInterface
{
    private RouteCollection $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function loadRoutes(): RouteCollection
    {
        return $this->routes;
    }
}