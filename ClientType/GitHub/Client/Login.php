<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\Client;


use Andreo\OAuthClientBundle\Client\HttpParameterInterface;

final class Login implements HttpParameterInterface
{
    private const KEY = 'login';

    private string $login;

    public function __construct(string $login)
    {
        $this->login = $login;
    }

    public function set(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->login;

        return $httpParams;
    }
}
