<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


use Andreo\OAuthApiConnectorBundle\Client\MetaDataProviderInterface;

final class AuthorizationUri
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

    public function serializeState(): string
    {
        return $this->getState()->serialize();
    }

    public static function fromProvider(MetaDataProviderInterface $metaDataProvider): self
    {
        return new self($metaDataProvider::getAuthorizationUri());
    }
}
