<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken;

use DateTimeImmutable;

interface AccessTokenInterface
{
    public const KEY = 'access_token';

    public function getAccessToken(): string;

    public function getExpiredAt(): DateTimeImmutable;
}
