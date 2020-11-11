<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


use Symfony\Component\Routing\RouterInterface;

final class CallbackUri
{
    private const KEY = 'redirect_uri';

    private string $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function generate(RouterInterface $router, ClientId $clientId, ZoneId $zoneId): self
    {
        $params = $zoneId->mapRequestParams();
        $params = $clientId->mapName($params);

        $new = clone $this;
        $new->uri = $router->generate(
            $this->uri,
            $params,
            RouterInterface::ABSOLUTE_URL
        );

        return $new;
    }

    public function mapRequestParams(array $requestParams = []): array
    {
        $requestParams[self::KEY] = $this->uri;

        return $requestParams;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
