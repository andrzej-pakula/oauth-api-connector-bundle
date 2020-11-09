<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
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
        $attributeBag = AttributeBag::get($request);
        $attributeBag = $attributeBag->buildCallbackUri($this->router)->save($request);
        if ($attributeBag->hasCallbackResponse()) {
            return $stack->next()($request, $response, $stack);
        }

        $attributeBag->buildAuthorizationUri()->save($request);

        return $stack->next()($request, $response, $stack);
    }
}
