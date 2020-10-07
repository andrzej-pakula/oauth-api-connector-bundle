<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class Attributes
{
    private const KEY = 'o_auth';

    private ClientId $clientId;

    private ClientSecret $clientSecret;

    private AuthorizationUri $authorizationUri;

    private CallbackUri $callbackUri;

    private ?CallbackParameters $callbackParameters;

    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        CallbackUri $callbackUri,
        AuthorizationUri $authorizationUri
    ){
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->callbackUri = $callbackUri;
        $this->authorizationUri = $authorizationUri;
    }

    public static function getFromRequest(Request $request): self
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

    public function createAuthorizationUri(): self
    {
        $new = clone $this;
        $new->authorizationUri = $this->authorizationUri->create($this->callbackUri, $this->clientId);

        return $new;
    }

    public function createCallbackUri(RouterInterface $router): self
    {
        $new = clone $this;
        $new->callbackUri = $this->callbackUri->generate($router, $this->clientId, $this->callbackParameters->getZoneId());

        return $new;
    }

    public function save(Request $request): self
    {
        $request->attributes->set(self::KEY, $this);

        return $this;
    }

    public function getCallbackParameters(): CallbackParameters
    {
        return $this->callbackParameters;
    }

    public function hasCallbackResponse(): bool
    {
        return $this->callbackParameters->hasCallbackResponse();
    }

    public function handleCallback(Request $request): self
    {
        $new = clone $this;
        $new->callbackParameters = CallbackParameters::fromRequest($request);

        return $new;
    }

    public function getState(): State
    {
        return $this->authorizationUri->getState();
    }

    public function getZoneId(): ZoneId
    {
        if (null === $this->callbackParameters) {
            throw new RuntimeException('Unhandled callback.');
        }

        return $this->callbackParameters->getZoneId();
    }

    public static function fromConfig(array $config): self
    {
        return new self(
            new ClientId((string)$config['client_id'], $config['client_name']),
            new ClientSecret($config['client_secret']),
            new CallbackUri($config['callback_uri']),
            new AuthorizationUri($config['authorization_uri'])
        );
    }
}
