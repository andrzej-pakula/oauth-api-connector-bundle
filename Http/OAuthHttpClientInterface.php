<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Http;

use Andreo\OAuthClientBundle\Client\AccessToken\AccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\GetAccessTokenInterface;
use Andreo\OAuthClientBundle\Client\AccessToken\Query\RefreshAccessTokenInterface;
use GuzzleHttp\ClientInterface;

interface OAuthHttpClientInterface extends ClientInterface
{
    public function getAccessToken(GetAccessTokenInterface $getAccessToken): AccessTokenInterface;

    public function refreshAccessToken(RefreshAccessTokenInterface $refreshAccessToken): AccessTokenInterface;
}
