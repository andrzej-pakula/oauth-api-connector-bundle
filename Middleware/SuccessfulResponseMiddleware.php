<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\Attributes;
use Andreo\OAuthApiConnectorBundle\Client\Attribute\Zone;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class SuccessfulResponseMiddleware implements MiddlewareInterface
{
    /**
     * @var iterable<string, Zone>
     */
    private iterable $zones;

    private RouterInterface $router;

    public function __construct(RouterInterface $router, iterable $zones = [])
    {
        $this->router = $router;
        $this->zones = $zones;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        if (!AccessToken::existAndValid($attributes->getClientId(), $request->getSession())) {
            throw new RuntimeException('Unhandled response. Reason: access token does not exist or not set.');
        }

        if (empty($this->zones)) {
            return new RedirectResponse($request->getBaseUrl());
        }

        $zoneId = $attributes->getZoneId()->getId();

        /** @var Zone|null $zone */
        $zone = $this->zones[$zoneId] ?? null;
        if (null === $zone) {
            throw new RuntimeException("Unhandled response. Zone '$zoneId' is not defined. Please check your configuration.");
        }

        try {
            $uri = $this->router->generate($zone->getSuccessfulResponseUri());
        } catch (\Exception $exception) {
            $uri = $zone->getSuccessfulResponseUri();
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param iterable $zones
     */
    public function withZonesConfigs(iterable $configs): self
    {
        $zones = [];
        foreach ($configs as $config) {
            $zones[] = Zone::fromConfig($config);
        }
        $new = clone $this;
        $new->router = $this->router;
        $new->zones = $zones;

        return $new;
    }
}
