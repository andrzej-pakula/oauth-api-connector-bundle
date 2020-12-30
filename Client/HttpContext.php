<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

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
        $param = $this->request->query->get(self::CODE_PARAM);
        assert(is_string($param));

        return $param;
    }

    public function getState(): State
    {
        if (!$this->isCallback()) {
            throw new InvalidCallbackResponseException();
        }

        $param = $this->request->query->get(State::KEY);
        assert(is_string($param));

        return new State($param);
    }

    public function isZoneSet(): bool
    {
        return !empty($this->request->attributes->get(ZoneId::KEY));
    }

    public function getZone(): ZoneId
    {
        if (!$this->isZoneSet()) {
            throw new MissingZoneException();
        }

        $param = $this->request->attributes->get(ZoneId::KEY);

        return new ZoneId($param);
    }

    public function isCallback(): bool
    {
        return !empty($this->request->query->get(self::CODE_PARAM)) && !empty($this->request->query->has(State::KEY));
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
