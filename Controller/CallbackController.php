<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Controller;


use Andreo\OAuthApiConnectorBundle\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CallbackController implements CallbackControllerInterface
{
    public function call(Request $request, ClientInterface $client): Response
    {
        return $client->connect($request);
    }
}
