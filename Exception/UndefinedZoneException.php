<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Exception;


use Andreo\OAuthClientBundle\Client\RedirectUri\ZoneId;
use LogicException;
use Throwable;

final class UndefinedZoneException extends LogicException implements OAuthClientException
{
    public function __construct(ZoneId $zoneId, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Zone=$zoneId is not defined", $code, $previous);
    }
}
