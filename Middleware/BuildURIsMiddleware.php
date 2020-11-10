<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Middleware;


use Andreo\OAuthClientBundle\Client\RequestContext\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class BuildURIsMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(Request $request, Response $response, MiddlewareStackInterface $stack): Response
    {
        $context = Context::get($request);
        $context = $context->buildCallbackUri($this->router)->save($request);
        if ($context->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $context->buildAuthorizationUri()->save($request);

        return $stack->next()($request, $response, $stack);
    }
}
