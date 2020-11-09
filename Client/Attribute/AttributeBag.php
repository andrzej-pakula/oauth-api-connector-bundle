<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;

use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class AttributeBag
{
    private const KEY = 'o_auth';

    private ClientId $clientId;

    private ClientSecret $clientSecret;

    private AuthorizationUri $authorizationUri;

    private CallbackUri $callbackUri;

    private Parameters $parameters;

    /**
     * @var iterable<string, Zone>
     */
    private iterable $zones;

    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        CallbackUri $callbackUri,
        AuthorizationUri $authorizationUri,
        iterable $zones
    ){
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->callbackUri = $callbackUri;
        $this->authorizationUri = $authorizationUri;
        $this->zones = $zones;
    }

    public static function get(Request $request): self
    {
        return $request->attributes->get(self::KEY);
    }

    public function getClientId(): ClientId
    {
        return $this->clientId;
    }

    public function getClientSecret(): ClientSecret
    {
        return $this->clientSecret;
    }

    public function getAuthorizationUri(): AuthorizationUri
    {
        return $this->authorizationUri;
    }

    public function getCallbackUri(): CallbackUri
    {
        return $this->callbackUri;
    }

    public function buildAuthorizationUri(): self
    {
        $new = clone $this;
        $new->authorizationUri = $this->authorizationUri->create($this->callbackUri, $this->clientId);

        return $new;
    }

    public function buildCallbackUri(RouterInterface $router): self
    {
        $new = clone $this;
        $new->callbackUri = $this->callbackUri->generate($router, $this->clientId, $this->parameters->getZoneId());

        return $new;
    }

    public function save(Request $request): self
    {
        $request->attributes->set(self::KEY, $this);

        return $this;
    }

    public function getParameters(): Parameters
    {
        return $this->parameters;
    }

    public function hasCallbackResponse(): bool
    {
        return $this->parameters->isCallback();
    }

    public function handleRequest(Request $request): self
    {
        $parameters = Parameters::from($request);
        if (!$this->isEmptyZones() && !$parameters->isZoneDefined()) {
            throw new RuntimeException('Request requires zone parameter.');
        }

        $new = clone $this;
        $new->parameters = $parameters;

        return $new;
    }

    public function getState(): State
    {
        return $this->authorizationUri->getState();
    }

    public function hasZone(): bool
    {
        return !empty($this->zones[$this->parameters->getZoneId()->getId()]);
    }

    public function isEmptyZones(): bool
    {
        return empty($this->zones);
    }

    public function getZone(): Zone
    {
        if (!$this->hasZone()) {
            throw new RuntimeException("Zone={$this->parameters->getZoneId()->getId()} is not defined.");
        }

        return $this->zones[$this->parameters->getZoneId()->getId()];
    }

    public static function fromConfig(array $config): self
    {
        return new self(
            new ClientId((string)$config['client_id'], $config['client_name']),
            new ClientSecret($config['client_secret']),
            new CallbackUri($config['callback_uri']),
            AuthorizationUri::fromConfig($config),
            Zone::createRegistryByConfig($config)
        );
    }
}
