<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;


use Andreo\OAuthClientBundle\Client\AuthorizationUri\AuthorizationUri;
use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;
use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;
use Andreo\OAuthClientBundle\Exception\UndefinedZoneException;
use Symfony\Component\Routing\RouterInterface;

final class ClientContext
{
    private ClientName $clientName;

    private RedirectUri $redirectUri;

    private AuthorizationUri $authorizationUri;

    /**
     * @var array<string, Zone>
     */
    private iterable $zones;

    public function __construct(
        ClientName $clientName,
        RedirectUri $redirectUri,
        AuthorizationUri $authorizationUri,
        array $zones
    ){
        $this->clientName = $clientName;
        $this->redirectUri = $redirectUri;
        $this->authorizationUri = $authorizationUri;
        $this->zones = $zones;
    }

    public function hasZone(ZoneId $zoneId): bool
    {
        return !empty($this->zones[$zoneId->getId()]);
    }

    public function hasZones(): bool
    {
        return !empty($this->zones);
    }

    public function getZone(ZoneId $zoneId): Zone
    {
        if (!$this->hasZone($zoneId)) {
            throw new UndefinedZoneException($zoneId);
        }

        return $this->zones[$zoneId->getId()];
    }

    public function getClientName(): ClientName
    {
        return $this->clientName;
    }

    public function getRedirectUri(): RedirectUri
    {
        return $this->redirectUri;
    }

    public function getAuthorizationUri(): AuthorizationUri
    {
        return $this->authorizationUri;
    }

    public function createAuthorizationUri(RedirectUri $redirectUri): self
    {
        $new = clone $this;
        $new->authorizationUri  = $this->authorizationUri
            ->addHTTPParameter($redirectUri)
            ->createState()
            ->createUri();

        return $new;
    }

    public function createRedirectUri(RouterInterface $router, ?ZoneId $zoneId): self
    {
        $new = clone $this;
        if (null !== $zoneId) {
            $redirectUri = $this->redirectUri->addHTTPParameter($zoneId);
        } else {
            $redirectUri = $this->redirectUri;
        }

        $new->redirectUri = $redirectUri->createUri($router);

        return $new;
    }

    public function getZones(): array
    {
        return $this->zones;
    }
}
