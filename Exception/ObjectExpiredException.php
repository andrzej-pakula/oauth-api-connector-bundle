<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Exception;

use Andreo\OAuthClientBundle\Storage\ExpiringInterface;
use LogicException;
use Throwable;

final class ObjectExpiredException extends LogicException
{
    public function __construct(ExpiringInterface $storable, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Object of type:'.get_class($storable).'has expired', $code, $previous);
    }
}
