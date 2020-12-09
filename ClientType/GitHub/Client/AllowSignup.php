<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\Client;


use Andreo\OAuthClientBundle\Client\HttpParameterInterface;

final class AllowSignup implements HttpParameterInterface
{
    private const KEY = 'allow_signup';

    private bool $allow;

    public function __construct(bool $allow)
    {
        $this->allow = $allow;
    }

    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->allow;

        return $httpParams;
    }
}
