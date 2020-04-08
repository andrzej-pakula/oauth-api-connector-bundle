<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;


class AccessToken
{
    private string $accessToken;
    private string $tokenType;
    private string $expiresIn;

    public function __construct(string $accessToken, string $tokenType, string $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): string
    {
        return $this->expiresIn;
    }
}