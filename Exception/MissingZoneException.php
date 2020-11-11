<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Exception;


use LogicException;
use Throwable;

final class MissingZoneException extends LogicException implements OAuthClientException
{
    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Missing zone parameter in current request.', $code, $previous);
    }
}
