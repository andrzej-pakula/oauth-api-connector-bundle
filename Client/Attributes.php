<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

final class Attributes
{
    private const KEY = 'o_auth';

    private ClientId $clientId;

    private ClientSecret $clientSecret;

    private AuthorizationUrl $authorizationUrl;

    private CallbackUrl $callbackUrl;

    private ?CallbackParameters $callbackParameters;

    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        CallbackUrl $callbackUrl,
        AuthorizationUrl $authorizationUrl
    ){
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->callbackUrl = $callbackUrl;
        $this->authorizationUrl = $authorizationUrl;
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

    public function getAuthorizationUrl(): AuthorizationUrl
    {
        return $this->authorizationUrl;
    }

    public function getCallbackUrl(): CallbackUrl
    {
        return $this->callbackUrl;
    }

    public function createAuthorizationUrl(): self
    {
        $new = clone $this;
        $new->authorizationUrl = $this->authorizationUrl->createUrl($new->callbackUrl);

        return $new;
    }

    public function generateCallbackUrl(RouterInterface $router): self
    {
        $new = clone $this;
        $new->callbackUrl = $this->callbackUrl->generate($router, $this->callbackParameters->getZoneId());

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
        return $this->authorizationUrl->getState();
    }

    public function getZoneId(): ZoneId
    {
        if (null === $this->callbackParameters) {
            throw new RuntimeException('Unhandled callback.');
        }

        return $this->callbackParameters->getZoneId();
    }
}
