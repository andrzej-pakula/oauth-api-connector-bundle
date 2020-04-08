<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;

use Andreo\OAuthApiConnectorBundle\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiConnectorController extends AbstractController implements ApiConnectorControllerInterface
{
    public function connect(Request $request, ApiConnectorInterface $apiConnector): Response
    {
        if ($apiConnector->hasCode() && $apiConnector->isValidState()) {
            $accessToken = $apiConnector->askAccessToken();
            $apiConnector->keepToken($accessToken);

            return $this->redirectToRoute($apiConnector->getRedirectRoute());
        }

        throw new ConnectionException('Unauthorized call.');
    }

    public function renderLoginView(string $description, ApiConnectorInterface $apiConnector): Response
    {
        return $this->render('@AndreoOAuthApiConnector/login.html.twig', [
            'redirect_url' => $apiConnector->getLoginURL(),
            'description' => $description,
        ]);
    }
}