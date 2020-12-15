<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage;

use DateTimeImmutable;

interface ThisIsExpiringInterface
{
    public function getExpiredAt(): DateTimeImmutable;
}
