<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client\AccessToken\Query;

use Andreo\OAuthClientBundle\Client\RedirectUri\RedirectUri;

interface GetAccessTokenInterface
{
    public function getClientId(): string;

    public function getRedirectUri(): string;

    public function getClientSecret(): string;

    public function getCode(): string;

    public function withRedirectUri(RedirectUri $redirectUri): self;

    public function withCode(string $code): self;
}
