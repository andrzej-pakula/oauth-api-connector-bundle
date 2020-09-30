<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Symfony\Component\HttpFoundation\Request;

final class Code
{
    private const KEY = 'code';

    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public static function fromRequest(Request $request): ?self
    {
        if (null === $code = $request->query->get(self::KEY)) {
            return null;
        }

        return new self($code);
    }
}
