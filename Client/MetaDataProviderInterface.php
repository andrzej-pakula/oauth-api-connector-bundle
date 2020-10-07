<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


interface MetaDataProviderInterface
{
    public static function getApiUri(): string;

    public static function getAuthorizationUri(): string;

    public static function getType(): string;

    public static function getVersion(): ?string;
}
