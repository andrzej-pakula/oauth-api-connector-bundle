<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ClientInterface
{
    public function handle(Request $request): Response;
}
