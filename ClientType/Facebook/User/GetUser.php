<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\ClientType\Facebook\User;


use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\GuzzleBundle\DataTransfer\RequestTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\ResponseTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\Type\DataType;
use Andreo\OAuthClientBundle\User\Query\GetUserInterface;

final class GetUser implements GetUserInterface, DataTransferInterface
{
    private string $accessToken;

    private string $fields;

    public function __construct(string $accessToken, array $fields)
    {
        $this->accessToken = $accessToken;
        $this->fields = implode(',', $fields);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getFields(): string
    {
        return $this->fields;
    }

    public function transfer(RequestTransformerInterface $transformer): RequestTransformerInterface
    {
        return $transformer->withQuery($this);
    }

    public function reverseTransfer(ResponseTransformerInterface $transformer): ResponseTransformerInterface
    {
        return $transformer->withData(DataType::single(User::class));
    }
}
