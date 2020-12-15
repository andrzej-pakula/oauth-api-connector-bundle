<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Exception;

use Andreo\OAuthClientBundle\Storage\ThisIsExpiringInterface;
use LogicException;
use Throwable;

final class ObjectExpiredException extends LogicException
{
    public function __construct(ThisIsExpiringInterface $storable, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Object of type:'.get_class($storable).'has expired', $code, $previous);
    }
}
