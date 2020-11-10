<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook;


final class Versions
{
    public const V7 = 'v7.0';
    public const V8 = 'v8.0';

    public const ALL = [
        self::V7,
        self::V8,
    ];

    public const LATEST = self::V8;
}
