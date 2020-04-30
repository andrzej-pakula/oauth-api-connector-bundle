<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Provider;


interface ApiConnectorProviderInterface
{
    public function getLoginURI(): ?string;

    public function getApiURI(): string;

    public function getApiVersion(): ?string;

    public function getLoginPath(): string;

    public function getAccessTokenPath(): string;

    public function getName(): string;
}