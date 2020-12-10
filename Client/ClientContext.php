<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\AuthorizationUri;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\State;
use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;
use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;
use Andreo\OAuthClientBundle\Exception\UndefinedZoneException;
use Andreo\OAuthClientBundle\Util\KeyGenerator;
use Symfony\Component\Routing\RouterInterface;

final class ClientContext
{
    private ClientId $id;

    private ClientSecret $secret;

    private ClientName $name;

    private RedirectUri $redirectUri;

    private AuthorizationUri $authorizationUri;

    /**
     * @var array<string, Zone>
     */
    private iterable $zones;

    public function __construct(
        ClientId $id,
        ClientSecret $secret,
        ClientName $name,
        RedirectUri $redirectUri,
        AuthorizationUri $authorizationUri,
        array $zones
    ) {
        $this->id = $id;
        $this->secret = $secret;
        $this->name = $name;
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

    public function getId(): ClientId
    {
        return $this->id;
    }

    public function getSecret(): ClientSecret
    {
        return $this->secret;
    }

    public function getName(): ClientName
    {
        return $this->name;
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
        $new->authorizationUri = $this->authorizationUri
            ->createState()
            ->addHttpParameter($redirectUri)
            ->createUri();

        return $new;
    }

    public function createRedirectUri(RouterInterface $router, ?ZoneId $zoneId): self
    {
        $new = clone $this;
        if (null !== $zoneId) {
            $redirectUri = $this->redirectUri->addHttpParameter($zoneId);
        } else {
            $redirectUri = $this->redirectUri;
        }

        $new->redirectUri = $redirectUri->createUri($router);

        return $new;
    }

    public function getTokenStorageKey(): string
    {
        return KeyGenerator::get($this->name, AccessTokenInterface::KEY);
    }

    public function getStateStorageKey(): string
    {
        return KeyGenerator::get($this->name, State::KEY);
    }
}
