<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\ClientType\Facebook\Http;


use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Http\OAuthClientInterface;
use GuzzleHttp\ClientInterface;

final class OAuthHTTPClient implements OAuthClientInterface, ClientInterface
{
    use ClientDecoratorTrait;

    public function getAccessToken(DataTransferInterface $accessTokenQuery): AccessToken
    {
        return $this->get('oauth/access_token', [
            'data' => $accessTokenQuery
        ])->getData();
    }
}
