<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Http;

use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\OAuthClientBundle\AccessToken\AccessToken;

interface OAuthClientInterface
{
    public function getAccessToken(DataTransferInterface $accessTokenQuery): AccessToken;
}
