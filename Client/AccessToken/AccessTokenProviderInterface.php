<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken;

use Andreo\OAuthClientBundle\Client\ClientContext;
use Andreo\OAuthClientBundle\Client\HttpContext;

interface AccessTokenProviderInterface
{
    public function getAccessToken(HttpContext $httpContext, ClientContext $clientContext): AccessTokenInterface;
}
