<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\HTTPClient;

use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;

interface OAuthClientInterface
{
    public function getAccessToken(DataTransferInterface $accessTokenQuery): AccessToken;
}
