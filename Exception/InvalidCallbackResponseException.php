<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Exception;


use RuntimeException;
use Throwable;

final class InvalidCallbackResponseException extends RuntimeException implements OAuthClientException
{
    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Invalid callback response.', $code, $previous);
    }
}
