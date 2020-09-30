<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Symfony\Component\Routing\RouterInterface;

final class CallbackUrl
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function generate(RouterInterface $router, ZoneId $zoneId): self
    {
        $new = clone $this;
        $new->url = $router->generate(
            $this->url,
            $zoneId->mapRequestParams(),
            RouterInterface::ABSOLUTE_URL
        );

        return $new;
    }

    public function mapRequestParams(array $requestParams = []): array
    {
        $requestParams['redirect_uri'] = $this->url;

        return $requestParams;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
