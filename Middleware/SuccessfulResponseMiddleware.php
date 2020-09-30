<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Middleware;


use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attributes;
use Andreo\OAuthApiConnectorBundle\Client\Zone;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SuccessfulResponseMiddleware implements MiddlewareInterface
{
    /**
     * @var iterable<string, Zone>
     */
    private iterable $zones;

    public function __construct(iterable $zones)
    {
        $this->zones = $zones;
    }

    public function __invoke(Request $request, MiddlewareStackInterface $stack): Response
    {
        $attributes = Attributes::getFromRequest($request);
        if (!AccessToken::existAndValid($attributes->getClientId(), $request->getSession())) {
            throw new RuntimeException('Unhandled response. Reason: access token does not exist.');
        }

        $zoneId = $attributes->getZoneId()->getId();

        /** @var Zone|null $zone */
        $zone = $this->zones[$zoneId] ?? null;
        if (null === $zone) {
            throw new RuntimeException("Unhandled response. Zone '$zoneId' is not defined. Please check your configuration.");
        }

        return new RedirectResponse($zone->getSuccessfulResponseUrl());
    }
}
