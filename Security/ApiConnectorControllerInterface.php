<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApiConnectorControllerInterface
{
    public function connect(Request $request, ApiConnectorInterface $client): Response;

    public function renderLoginView(string $description, ApiConnectorInterface $client): Response;
}