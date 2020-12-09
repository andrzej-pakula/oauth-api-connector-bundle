<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Exception;


use LogicException;
use Throwable;

class StorableNotExistException extends LogicException
{
    public function __construct(string $key, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Key=' . $key . 'not exist in storage.', $code, $previous);
    }
}
