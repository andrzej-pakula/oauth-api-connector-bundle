<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\User\Query;

interface GetUserInterface
{
    public function getAccessToken(): ?string;
}
