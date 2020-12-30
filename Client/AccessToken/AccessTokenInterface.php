<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken;

use Andreo\OAuthClientBundle\User\UserInterface;

interface AccessTokenInterface
{
    public const KEY = 'access_token';

    public function getAccessToken(): string;

    public function withUser(UserInterface $user): self;

    public function getUser(): UserInterface;
}
