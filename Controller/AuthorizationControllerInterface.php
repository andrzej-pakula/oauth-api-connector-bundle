<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Controller;


use Andreo\OAuthClientBundle\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface AuthorizationControllerInterface
{
    public function handle(Request $request, ClientInterface $client): Response;
}
