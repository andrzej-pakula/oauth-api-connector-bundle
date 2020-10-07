<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

final class CallbackParameters
{
    private ?Code $code;

    private ?State $state;

    private ZoneId $zoneId;

    public function __construct(?Code $code, ?State $state, ZoneId $zoneId)
    {
        $this->code = $code;
        $this->state = $state;
        $this->zoneId = $zoneId;
    }

    public static function fromRequest(Request $request): self
    {
        if (null === $zoneId = ZoneId::fromRequest($request)) {
            throw new RuntimeException('Missing selected zone.');
        }

        return new self(
            Code::fromRequest($request),
            State::fromRequest($request),
            $zoneId
        );
    }

    public function getCode(): Code
    {
        if (!$this->hasCallbackResponse()) {
            throw new RuntimeException('Code is not in callback response.');
        }
        return $this->code;
    }

    public function getState(): State
    {
        if (!$this->hasCallbackResponse()) {
            throw new RuntimeException('State is not in callback response.');
        }

        return $this->state;
    }

    public function getZoneId(): ZoneId
    {
        return $this->zoneId;
    }

    public function hasCallbackResponse(): bool
    {
        return null !== $this->code && null !== $this->state;
    }
}
