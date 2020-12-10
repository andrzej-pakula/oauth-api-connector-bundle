<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\GitHub;

final class Versions
{
    public const V3 = 'v3';
    public const V4 = 'v4';

    public const ALL = [
        self::V3,
        self::V4,
    ];

    public const LATEST = self::V4;
}
