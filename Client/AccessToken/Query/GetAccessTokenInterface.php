<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken\Query;

interface GetAccessTokenInterface
{
    public function getClientId(): string;

    public function getRedirectUri(): string;

    public function getClientSecret(): string;

    public function getCode(): ?string;
}
