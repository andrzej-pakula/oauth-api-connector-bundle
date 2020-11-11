<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;


use RuntimeException;
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

    public static function fromRequest(Request $request): self
    {
        if (!self::inRequest($request)) {
            throw new RuntimeException('Missing code parameter.');
        }

        $code = $request->query->get(self::KEY);

        return new self($code);
    }

    public static function inRequest(Request $request): bool
    {
        return $request->query->has(self::KEY);
    }
}
