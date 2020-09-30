<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


interface MiddlewareStackInterface
{
    public function next(): MiddlewareInterface;
}
