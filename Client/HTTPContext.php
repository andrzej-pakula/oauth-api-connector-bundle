<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;

use Andreo\OAuthClientBundle\Client\RedirectUri\Parameters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class HTTPContext
{
    private Request $request;

    private Response $response;

    private Parameters $parameters;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->parameters = Parameters::from($request);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function isCallback(): bool
    {
        return $this->parameters->isCallback();
    }

    public function getParameters(): Parameters
    {
        return $this->parameters;
    }

    public function withResponse(Response $response): self
    {
        $new = clone $this;
        $new->response = $response;

        return $new;
    }
}
