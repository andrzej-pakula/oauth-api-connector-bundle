<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage;

use DateTimeImmutable;

interface ExpiringInterface
{
    public function getExpiredAt(): DateTimeImmutable;
}
