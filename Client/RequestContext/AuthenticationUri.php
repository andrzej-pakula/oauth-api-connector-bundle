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

    public function create(CallbackUri $callbackUrl, ClientId $clientId): self
    {
        $new = clone $this;
        $new->state = State::create();

        $new->uri = $this->uri . '?' . $new->getQuery($callbackUrl, $clientId);

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

    private function getQuery(CallbackUri $callbackUrl, ClientId $clientId): string
    {
        $params =  [];
        $params = $callbackUrl->mapRequestParams($params);
        $params = $clientId->mapId($params);
        $params = $this->state->mapRequestParams($params);

        return http_build_query($params);
    }

    public static function fromConfig(array $config): self
    {
        return new self($config['auth_uri']);
    }
}
