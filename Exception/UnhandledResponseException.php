<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Exception;

use LogicException;
use Throwable;

final class UnhandledResponseException extends LogicException implements OAuthClientException
{
    public function __construct(?string $message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Unhandled response.', $code, $previous);
    }
}
