<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Exception;

use LogicException;
use Throwable;

final class ExpiredAccessTokenException extends LogicException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Access token has expired', $code, $previous);
    }
}
