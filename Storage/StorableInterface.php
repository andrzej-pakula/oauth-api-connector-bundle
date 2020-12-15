<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage;

interface StorableInterface
{
    public function mayBeExpired(): bool;
}
