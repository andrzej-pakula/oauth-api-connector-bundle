<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Provider;


class FBApiConnectorProvider implements ApiConnectorProviderInterface
{
    private const CONNECTOR_NAME = 'facebook';
    private const API_VERSION = 'v6.0';
    private const LOGIN_URI = 'https://www.facebook.com';
    private const API_URI = 'https://graph.facebook.com';

    public function getLoginPath(): string
    {
        return '/dialog/oauth';
    }

    public function getAccessTokenPath(): string
    {
        return '/oauth/access_token';
    }

    public function getApiURI(): string
    {
        return self::API_URI;
    }

    public function getLoginURI(): ?string
    {
        return self::LOGIN_URI;
    }

    public function getApiVersion(): ?string
    {
        return self::API_VERSION;
    }

    public function getName(): string
    {
        return self::CONNECTOR_NAME;
    }
}