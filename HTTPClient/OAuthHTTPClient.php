<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\HTTPClient;


use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use GuzzleHttp\ClientInterface;

final class OAuthHTTPClient implements OAuthClientInterface, ClientInterface
{
    use ClientDecoratorTrait;

    public function getAccessToken(DataTransferInterface $accessTokenQuery): AccessToken
    {
        return $this->get('?', [
            'data' => $accessTokenQuery
        ])->getData();
    }
}
