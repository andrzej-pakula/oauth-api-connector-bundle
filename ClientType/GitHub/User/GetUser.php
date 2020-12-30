<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\GitHub\User;


use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\GuzzleBundle\DataTransfer\RequestTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\ResponseTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\Type\DataType;
use Andreo\OAuthClientBundle\User\Query\GetUserInterface;

final class GetUser implements GetUserInterface, DataTransferInterface
{
    private ?string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;}

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function transfer(RequestTransformerInterface $transformer): RequestTransformerInterface
    {
        $this->accessToken = null;
        return $transformer->withQuery($this);
    }

    public function reverseTransfer(ResponseTransformerInterface $transformer): ResponseTransformerInterface
    {
        return $transformer->withData(DataType::single(User::class));
    }
}
