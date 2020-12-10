<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken;

use Andreo\OAuthClientBundle\Storage\StorableInterface;
use DateInterval;
use DateTimeImmutable;

final class AccessToken implements AccessTokenInterface, StorableInterface
{
    private string $accessToken;

    private string $tokenType;

    private DateTimeImmutable $expiredAt;

    public function __construct(string $accessToken, string $tokenType, int $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiredAt = (new DateTimeImmutable())->add(new DateInterval("PT{$expiresIn}S"));
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }
}
