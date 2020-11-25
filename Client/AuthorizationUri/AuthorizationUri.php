<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\AuthorizationUri;


use Andreo\OAuthClientBundle\Client\AggregateHTTPParamInterface;
use Andreo\OAuthClientBundle\Client\UriComposingInterface;

final class AuthorizationUri implements UriComposingInterface
{
    private string $uri;

    private State $state;

    /**
     * @var AggregateHTTPParamInterface[]
     */
    private array $httpParameters;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
        $this->httpParameters = [];
    }

    public function createState(): self
    {
        $new = clone $this;
        $new->state = State::create();
        $new->httpParameters[] = $new->state;

        return $new;
    }

    public function addHTTPParameter(AggregateHTTPParamInterface $httpParam): self
    {
        $new = clone $this;
        $new->httpParameters[] = $httpParam;

        return $new;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function createUri(): self
    {
        $new = clone $this;
        $parameters = [];
        foreach ($this->httpParameters as $httpParam) {
            $parameters = $httpParam->aggregateParam($parameters);
        }

        $new->uri = $this->uri . '?' . http_build_query($parameters);

        return $new;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
