<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ClientType;


final class SupportedType
{
    public const FACEBOOK = 'facebook';
    public const GITHUB = 'github';

    public const ALL = [
        self::FACEBOOK,
        self::GITHUB
    ];
}
