<?php

declare(strict_types=1);


namespace Tests\Andreo\OAuthApiConnectorBundle\App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestController extends AbstractController
{
    /**
     * @Route("index", name="app.test.index")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("success", name="app.auth_test_successful")
     */
    public function successful(Request $request): Response
    {
        return new Response('ok');
    }
}
