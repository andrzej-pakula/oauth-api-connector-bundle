<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


final class AuthenticationUri
{
    private string $uri;

    private State $state;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function create(CallbackUri $callbackUri, ClientId $clientId, Scope $scope): self
    {
        $new = clone $this;

        $new->state = State::create();

        $params = $callbackUri->mapQuery();
        $params = $scope->mapQuery($params);
        $params = $new->state->mapQuery($params);
        $params = $clientId->mapQueryId($params);

        $new->uri = $this->uri . '?' . http_build_query($params);

        return $new;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
