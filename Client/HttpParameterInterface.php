<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

interface HttpParameterInterface
{
    /**
     * @return array<string, string>
     */
    public function set(array $httpParams = []): array;
}
