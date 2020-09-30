<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\HTTPClient\Query;


use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\GuzzleBundle\DataTransfer\RequestTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\ResponseTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\Type\DataType;
use Andreo\OAuthApiConnectorBundle\AccessToken\AccessToken;
use Andreo\OAuthApiConnectorBundle\Client\Attributes;

final class AccessTokenQuery implements DataTransferInterface
{
    private string $clientId;

    private string $redirectUri;

    private string $clientSecret;

    private string $code;

    public function __construct(string $clientId, string $redirectUri, string $clientSecret, string $code)
    {
        $this->clientId = $clientId;
        $this->redirectUri = $redirectUri;
        $this->clientSecret = $clientSecret;
        $this->code = $code;
    }

    public static function fromAttributes(Attributes $attributes): self
    {
        return new self(
            $attributes->getClientID()->getId(),
            $attributes->getCallbackUrl()->getUrl(),
            $attributes->getClientSecret()->getSecret(),
            $attributes->getCallbackParameters()->getCode()->getCode()
        );
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getCode(): string
    {
        return $this->code;
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
