<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\AttributeBag;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\State;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\Zone;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class SuccessfulResponseMiddleware implements MiddlewareInterface
{
    private RouterInterface $router;

    /**
     * @var iterable<string, Zone>
     */
    private iterable $zones;

    public function __construct(RouterInterface $router, iterable $zones = [])
    {
        $this->router = $router;
        $this->zones = $zones;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributeBag = AttributeBag::get($request);
        if (!AccessToken::isStoredAndValid($attributeBag->getClientId(), $request->getSession())) {
            throw new RuntimeException('Unhandled response, because access token does not exist or not valid.');
        }

        if (empty($this->zones)) {
            return new RedirectResponse('/');
        }

        $zoneId = $attributeBag->getZoneId()->getId();

        /** @var Zone|null $zone */
        $zone = $this->zones[$zoneId] ?? null;
        if (null === $zone) {
            throw new RuntimeException("Unhandled response, because zone '$zoneId' was not found. Please check your configuration.");
        }

        try {
            $uri = $this->router->generate($zone->getSuccessfulResponseUri());
        } catch (\Exception $exception) {
            $uri = '/';
        }

        if (State::isStored($attributeBag->getClientId(), $request->getSession())) {
            State::delete($attributeBag->getClientId(), $request->getSession());
        }

        return new RedirectResponse($uri);
    }
}
