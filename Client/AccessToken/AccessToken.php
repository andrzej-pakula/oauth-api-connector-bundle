<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken;

use Andreo\OAuthClientBundle\Storage\StorableInterface;
use Andreo\OAuthClientBundle\User\UserInterface;

final class AccessToken implements AccessTokenInterface, StorableInterface
{
    private string $accessToken;

    private string $tokenType;

    private UserInterface $user;

    public function __construct(string $accessToken, string $tokenType)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function withUser(UserInterface $user): AccessTokenInterface
    {
        $new = clone $this;
        $new->user = $user;

        return $new;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
