<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AuthorizationUri;

use Andreo\OAuthClientBundle\Client\HttpParameterInterface;
use Andreo\OAuthClientBundle\Storage\Encoder\Encoder;
use Andreo\OAuthClientBundle\Storage\StorableInterface;
use Andreo\OAuthClientBundle\Storage\ThisIsExpiringInterface;
use DateInterval;
use DateTimeImmutable;

final class State implements HttpParameterInterface, StorableInterface, ThisIsExpiringInterface
{
    public const KEY = 'state';

    private string $state;

    private DateTimeImmutable $expiredAt;

    public function __construct(string $state)
    {
        $this->state = $state;
        $this->expiredAt = (new DateTimeImmutable())->add(new DateInterval('PT60S'));
    }

    public static function create(): self
    {
        return new self(Encoder::encode(random_bytes(16)));
    }

    public function equals(self $other): bool
    {
        return $this->state === $other->state;
    }

    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->state;

        return $httpParams;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }
}
