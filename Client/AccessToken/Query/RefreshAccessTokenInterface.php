<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken\Query;

interface RefreshAccessTokenInterface
{
    public function getClientId(): string;

    public function getClientSecret(): string;
}
