<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Controller;


use Andreo\OAuthApiConnectorBundle\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AuthorizeController implements AuthorizeControllerInterface
{
    /**
     * @Route("oauth/authorize/{client_name}/{zone}", name="andreo.oauth.client.authorize")
     */
    public function handle(Request $request, ClientInterface $client): Response
    {
        return $client->connect($request);
    }
}
