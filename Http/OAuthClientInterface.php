<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Http;


use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\AccessTokenQueryInterface;

interface OAuthClientInterface
{
    public function getAccessToken(AccessTokenQueryInterface $accessTokenQuery): AccessTokenInterface;
}
