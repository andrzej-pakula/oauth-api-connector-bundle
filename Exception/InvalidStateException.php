<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Exception;

use RuntimeException;
use Throwable;

final class InvalidStateException extends RuntimeException implements OAuthClientException
{
    public function __construct(string $message = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Invalid incoming state.', $code, $previous);
    }
}
