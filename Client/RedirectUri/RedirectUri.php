<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RedirectUri;


use Andreo\OAuthClientBundle\Client\AggregateHTTPParamInterface;
use Andreo\OAuthClientBundle\Client\UriComposingInterface;
use Symfony\Component\Routing\RouterInterface;

final class RedirectUri implements UriComposingInterface, AggregateHTTPParamInterface
{
    private const KEY = 'redirect_uri';

    /**
     * @var AggregateHTTPParamInterface[]
     */
    private array $httpParameters;

    private string $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
        $this->httpParameters = [];
    }

    public function createUri(RouterInterface $router): self
    {
        $parameters = [];
        foreach ($this->httpParameters as $parameter) {
            $parameters = $parameter->aggregateParam($parameters);
        }

        $new = clone $this;
        $new->uri = $router->generate(
            $this->uri,
            $parameters,
            RouterInterface::ABSOLUTE_URL
        );

        return $new;
    }

    public function addHTTPParameter(AggregateHTTPParamInterface $httpParam): self
    {
        $new = clone $this;
        $new->httpParameters[] = $httpParam;

        return $new;
    }

    public function aggregateParam(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->uri;

        return $httpParams;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
