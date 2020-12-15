<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Storage\StorableInterface;
use Andreo\OAuthClientBundle\Storage\ThisIsExpiringInterface;
use DateInterval;
use DateTimeImmutable;

final class AccessToken implements AccessTokenInterface, ThisIsExpiringInterface, StorableInterface
{
    private string $accessToken;

    private string $tokenType;

    private ?DateTimeImmutable $expiredAt;

    public function __construct(string $accessToken, string $tokenType, int $expiresIn = null)
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
