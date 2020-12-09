<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AuthorizationUri\State;
use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;
use Andreo\OAuthClientBundle\Exception\InvalidCallbackResponseException;
use Andreo\OAuthClientBundle\Exception\MissingAccessTokenException;
use Andreo\OAuthClientBundle\Exception\MissingZoneException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class HttpContext
{
    private const CODE_PARAM = 'code';

    private Request $request;

    private Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getSession(): SessionInterface
    {
        return $this->request->getSession();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getCode(): string
    {
        if (!$this->isCallback()) {
            throw new InvalidCallbackResponseException();
        }

        return $this->request->query->get(self::CODE_PARAM);
    }

    public function getState(): State
    {
        if (!$this->isCallback()) {
            throw new InvalidCallbackResponseException();
        }

        return new State($this->request->query->get(State::KEY));
    }

    public function isZoneSet(): bool
    {
        return $this->request->attributes->has(ZoneId::KEY);
    }

    public function getZone(): ZoneId
    {
        if (!$this->isZoneSet()) {
            throw new MissingZoneException();
        }

        return new ZoneId($this->request->attributes->get(ZoneId::KEY));
    }

    public function isCallback(): bool
    {
        return $this->request->query->has(self::CODE_PARAM) && $this->request->query->has(State::KEY);
    }

    public function withResponse(Response $response): self
    {
        $new = clone $this;
        $new->response = $response;

        return $new;
    }

    public function saveAccessToken(string $key, AccessTokenInterface $accessToken): void
    {
        $this->request->attributes->set($key, $accessToken);
    }

    public function getAccessToken(string $key): AccessTokenInterface
    {
        if (!$this->request->attributes->has($key)) {
            throw new MissingAccessTokenException('Can not save access token because it not exist.');
        }

        return $this->request->attributes->get($key);
    }

    public function removeAccessToken(string $key): void
    {
        $this->request->attributes->remove($key);
    }
}
