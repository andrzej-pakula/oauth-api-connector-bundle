<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Client\Attribute;


use Andreo\OAuthApiConnectorBundle\Traits\SerializeTrait;
use Andreo\OAuthApiConnectorBundle\Util\StoreKeyGenerator;
use RuntimeException;
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

    public static function create(): self
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

        $serialized = self::decode($state);

        return self::unserialize($serialized);
    }

    public function store(ClientId $clientId, SessionInterface $session): void
    {
        $session->set(StoreKeyGenerator::generate($clientId, self::KEY), $this->encode());
    }

    public static function isStored(ClientId $clientId, SessionInterface $session): bool
    {
        return $session->has(StoreKeyGenerator::generate($clientId, self::KEY));
    }

    public static function delete(ClientId $clientId, SessionInterface $session): void
    {
        if (!self::isStored($clientId, $session)) {
            throw new RuntimeException('Unable to delete non-stored state.');
        }

        $session->remove(StoreKeyGenerator::generate($clientId, self::KEY));
    }

    public static function get(ClientId $clientId, SessionInterface $session): self
    {
        if (!self::isStored($clientId, $session)) {
            throw new RuntimeException('State does not stored.');
        }

        $state = $session->get(StoreKeyGenerator::generate($clientId, self::KEY));

        return self::unserialize(self::decode($state));
    }


    public function mapRequestParams(array $requestParams): array
    {
        $requestParams[self::KEY] = $this->encode();

        return $requestParams;
    }
}
