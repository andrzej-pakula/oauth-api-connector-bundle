<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class SetZoneResponseMiddleware implements MiddlewareInterface
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
        $context = Context::get($request);

        if ($context->isEmptyZones()) {
            return $stack->next()($request, $response, $stack);
        }

        $uri = $this->router->generate($context->getZone()->getSuccessfulResponseUri());

        return $stack->next()($request, new RedirectResponse($uri), $stack);
    }
}
