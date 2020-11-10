<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\Http;


use Andreo\GuzzleBundle\Client\ClientDecoratorTrait;
use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\OAuthClientBundle\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Http\OAuthClientInterface;
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
