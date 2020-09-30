<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client;


use Andreo\OAuthApiConnectorBundle\Traits\SerializeTrait;
use Andreo\OAuthApiConnectorBundle\Util\KeyGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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

    public static function generate(): self
    {
        return new self(
            bin2hex(random_bytes(7)),
            random_int(1, 1000000)
        );
    }

    public function equals(self $other): bool
    {
        return $other->st === $this->st && $other->ds === $this->ds;
    }

    public static function fromRequest(Request $request): ?self
    {
        if (null === $state = $request->query->get(self::KEY)) {
            return null;
        }

        return self::unserialize($state);
    }

    public function store(ClientId $clientId, SessionInterface $session): void
    {
        $session->set(KeyGenerator::generate($clientId, self::KEY), $this->serialize());
    }

    public static function getFromStorage(ClientId $clientId, SessionInterface $session): ?self
    {
        if (null === $state = $session->get(KeyGenerator::generate($clientId, self::KEY))) {
            return null;
        }

        return self::unserialize($state);
    }


    public function mapRequestParams(array $requestParams): array
    {
        $requestParams[self::KEY] = $this->serialize();

        return $requestParams;
    }
}
