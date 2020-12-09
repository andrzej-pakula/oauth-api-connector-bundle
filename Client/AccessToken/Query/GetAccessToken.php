<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\AccessToken\Query;


use Andreo\GuzzleBundle\DataTransfer\DataTransferInterface;
use Andreo\GuzzleBundle\DataTransfer\RequestTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\ResponseTransformerInterface;
use Andreo\GuzzleBundle\DataTransfer\Type\DataType;
use Andreo\OAuthClientBundle\Client\AccessToken\AccessToken;

final class GetAccessToken implements GetAccessTokenInterface, DataTransferInterface
{
    use GetAccessTokenTrait;

    public function transfer(RequestTransformerInterface $transformer): RequestTransformerInterface
    {
        return $transformer->withQuery($this);
    }

    public function reverseTransfer(ResponseTransformerInterface $transformer): ResponseTransformerInterface
    {
        return $transformer->withData(DataType::single(AccessToken::class));
    }

}
