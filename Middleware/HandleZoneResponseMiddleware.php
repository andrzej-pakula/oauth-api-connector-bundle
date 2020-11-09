<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class HandleZoneResponseMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);

        if ($attributeBag->isEmptyZones()) {
            return $stack->next()($request, $response, $stack);
        }

        $uri = $this->router->generate($attributeBag->getZone()->getSuccessfulResponseUri());

        return $stack->next()($request, new RedirectResponse($uri), $stack);
    }
}
