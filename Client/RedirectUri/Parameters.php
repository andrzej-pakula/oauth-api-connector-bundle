<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RedirectUri;


use Andreo\OAuthClientBundle\Client\AuthorizationUri\State;
use Andreo\OAuthClientBundle\Exception\InvalidCallbackResponseException;
use Andreo\OAuthClientBundle\Exception\MissingZoneException;
use Symfony\Component\HttpFoundation\Request;

final class Parameters
{
    private ?Code $code;

    private ?State $state;

    private ?ZoneId $zoneId;

    public function __construct(?Code $code, ?State $state, ?ZoneId $zoneId)
    {
        $this->code = $code;
        $this->state = $state;
        $this->zoneId = $zoneId;
    }

    public static function from(Request $request): self
    {
        $code = Code::inRequest($request) ? Code::fromRequest($request) : null;
        $state = State::inRequest($request) ? State::fromRequest($request) : null;
        $zone = ZoneId::inRequest($request) ? ZoneId::fromRequest($request) : null;

        return new self(
            $code,
            $state,
            $zone
        );
    }

    public function getCode(): Code
    {
        if (!$this->isCallback()) {
            throw new InvalidCallbackResponseException();
        }
        return $this->code;
    }

    public function getState(): State
    {
        if (!$this->isCallback()) {
            throw new InvalidCallbackResponseException();
        }

        return $this->state;
    }

    public function getZoneId(): ZoneId
    {
        if (!$this->hasZoneId()) {
            throw new MissingZoneException();
        }

        return $this->zoneId;
    }

    public function hasZoneId(): bool
    {
        return null !== $this->zoneId;
    }

    public function isCallback(): bool
    {
        return null !== $this->code && null !== $this->state;
    }
}
