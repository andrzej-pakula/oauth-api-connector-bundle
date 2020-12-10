<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Middleware;

use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface
{
    public function __invoke(HttpContext $httpContext, ClientContext $clientContext, MiddlewareStackInterface $stack): Response;
}
