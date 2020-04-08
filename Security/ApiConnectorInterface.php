<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;


use Andreo\OAuthApiConnectorBundle\AccessToken;
use Symfony\Component\Routing\RouterInterface;

interface ApiConnectorInterface
{
    public function getCode(): string;

    public function hasCode(): bool;

    public function isValidState(): bool;

    public function askAccessToken(): AccessToken;

    public function keepToken(AccessToken $accessToken): void;

    public function getLoginURL(): string;

    public function getRedirectRoute(): string;
}