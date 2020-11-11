<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


use RuntimeException;
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
            throw new RuntimeException('Callback parameters are not complete.');
        }
        return $this->code;
    }

    public function getState(): State
    {
        if (!$this->isCallback()) {
            throw new RuntimeException('Callback parameters are not complete.');
        }

        return $this->state;
    }

    public function getZoneId(): ZoneId
    {
        if (!$this->isZoneDefined()) {
            throw new RuntimeException('Zone is not defined.');
        }

        return $this->zoneId;
    }

    public function isZoneDefined(): bool
    {
        return null !== $this->zoneId;
    }

    public function isCallback(): bool
    {
        return null !== $this->code && null !== $this->state;
    }
}
