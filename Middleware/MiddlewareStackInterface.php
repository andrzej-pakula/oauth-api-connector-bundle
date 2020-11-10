<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


interface MiddlewareStackInterface
{
    public function next(): MiddlewareInterface;
}
