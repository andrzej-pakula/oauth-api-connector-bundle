<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ClientInterface
{
    public function connect(Request $request): Response;
}
