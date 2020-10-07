<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Controller;


use Andreo\OAuthApiConnectorBundle\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface AuthorizeControllerInterface
{
    public function handle(Request $request, ClientInterface $client): Response;
}
