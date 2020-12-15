<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Exception;

use LogicException;
use Throwable;

final class MissingStateException extends LogicException implements OAuthClientException
{
    public function __construct(string $message = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Missing state in current session.', $code, $previous);
    }
}
