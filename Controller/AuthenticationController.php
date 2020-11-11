<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Controller;


use Andreo\OAuthClientBundle\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AuthenticationController implements AuthenticationControllerInterface
{
    /**
     * @Route(
     *     "oauth/authentication/{client}/{zone}",
     *     name="andreo.oauth.client.authentication",
     *     defaults={"zone": null},
     *     methods={Request::METHOD_GET}
     * )
     */
    public function handle(Request $request, ClientInterface $client): Response
    {
        return $client->handle($request);
    }
}
