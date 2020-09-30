<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


final class AuthorizationUrl
{
    private string $url;

    private ClientId $clientId;

    private State $state;

    public function __construct(string $url, ClientId $clientId)
    {
        $this->url = $url;
        $this->clientId = $clientId;
    }

    public function createUrl(CallbackUrl $callbackUrl): self
    {
        $new = clone $this;
        $new->state = State::generate();

        $new->url = $this->url . '?' . $this->getQuery($callbackUrl);

        return $new;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    private function getQuery(CallbackUrl $callbackUrl): string
    {
        $params =  [];
        $callbackUrl->mapRequestParams($params);
        $this->clientId->mapRequestParams($params);
        $this->state->mapRequestParams($params);

        return http_build_query($params);
    }

    public function serializeState(): string
    {
        return $this->getState()->serialize();
    }
}
