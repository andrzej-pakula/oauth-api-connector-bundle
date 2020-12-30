<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken\Query;

use Andreo\OAuthClientBundle\Client\ClientId;
use Andreo\OAuthClientBundle\Client\ClientSecret;
use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;

trait GetAccessTokenTrait
{
    private string $clientId;

    private string $clientSecret;

    private string $code;

    private string $redirectUri;

    public function __construct(ClientId $clientId, ClientSecret $clientSecret, string $code, RedirectUri $redirectUri)
    {
        $this->clientId = $clientId->getId();
        $this->clientSecret = $clientSecret->getSecret();
        $this->code = $code;
        $this->redirectUri = $redirectUri->getUri();
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }
}
