<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;


interface AggregateHTTPParamInterface
{
    /**
     * @return array<string, string>
     */
    public function aggregateParam(array $httpParams = []): array;
}
