<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client\RequestContext;

use Andreo\OAuthClientBundle\Traits\SerializeTrait;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

final class State
{
    use SerializeTrait;

    private const KEY = 'state';

    private string $st;

    private int $ds;

    public function __construct(string $st, int $ds)
    {
        $this->st = $st;
        $this->ds = $ds;
    }

    public function equals(self $other): bool
    {
        return $other->st === $this->st && $other->ds === $this->ds;
    }

    public static function isInRequest(Request $request): bool
    {
        return $request->query->has(self::KEY);
    }

    public static function from(Request $request): self
    {
        if (!self::isInRequest($request)) {
            throw new RuntimeException('Missing state parameter.');
        }

        $state = $request->query->get(self::KEY);

        return self::decrypt($state);
    }

    public static function create(): self
    {
        return new self(
            bin2hex(random_bytes(7)),
            random_int(1, 1000000)
        );
    }

    public function mapRequestParams(array $requestParams): array
    {
        $requestParams[self::KEY] = $this->encrypt();

        return $requestParams;
    }
}
