<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\AccessToken;


use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\GuzzleBundle\DataTransfer\RequestTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\ResponseTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\Type\DataType;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;
use Andreo\OAuthClientBundle\Client\ClientContext;

final class ExchangeAccessTokenQuery implements DataTransferInterface
{
    private string $clientId;

    private string $grantType;

    private string $clientSecret;

    private string $fbExchangeToken;

    public function __construct(string $clientId, string $clientSecret, string $fbExchangeToken, string $grantType = 'fb_exchange_token')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->fbExchangeToken = $fbExchangeToken;
        $this->grantType = $grantType;
    }

    public static function from(ClientContext $clientContext, AccessToken $accessToken): self
    {
        return new self(
            $clientContext->getClientId()->getId(),
            $clientContext->getClientSecret()->getSecret(),
            $accessToken->getAccessToken()
        );
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function getFbExchangeToken(): string
    {
        return $this->fbExchangeToken;
    }

    public function transfer(RequestTransformerInterface $transformer): RequestTransformerInterface
    {
        return $transformer->withQuery($this);
    }

    public function reverseTransfer(ResponseTransformerInterface $transformer): ResponseTransformerInterface
    {
        return $transformer->withData(DataType::single(AccessToken::class));
    }
}
