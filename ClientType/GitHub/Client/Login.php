<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\Client;


use Andreo\OAuthClientBundle\Client\AggregateHTTPParamInterface;

final class Login implements AggregateHTTPParamInterface
{
    private const KEY = 'login';

    private string $login;

    public function __construct(string $login)
    {
        $this->login = $login;
    }

    public function aggregateParam(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->login;

        return $httpParams;
    }
}
