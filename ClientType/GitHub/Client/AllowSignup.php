<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\Client;


use Andreo\OAuthClientBundle\Client\AggregateHTTPParamInterface;

final class AllowSignup implements AggregateHTTPParamInterface
{
    private const KEY = 'allow_signup';

    private bool $allow;

    public function __construct(bool $allow)
    {
        $this->allow = $allow;
    }

    public function aggregateParam(array $httpParams = []): array
    {
        $httpParams[self::KEY] = $this->allow;

        return $httpParams;
    }
}
