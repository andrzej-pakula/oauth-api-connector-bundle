<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\AccessToken;


use Andreo\OAuthApiConnectorBundle\Client\Attribute\ClientId;
use Andreo\OAuthApiConnectorBundle\Traits\SerializeTrait;
use Andreo\OAuthApiConnectorBundle\Util\StoreKeyGenerator;
use RuntimeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class AccessToken
{
    use SerializeTrait;

    public const KEY = 'access_token';

    private string $accessToken;

    private string $tokenType;

    private int $expiresIn;

    private bool $valid = true;

    public function __construct(string $accessToken, string $tokenType, int $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function invalid(): self
    {
        $new = clone $this;
        $new->valid = false;

        return $new;
    }

    public function store(ClientId $clientId, SessionInterface $session): void
    {
        $session->set(StoreKeyGenerator::generate($clientId, self::KEY), $this->encode());
    }

    public static function get(ClientId $clientId, SessionInterface $session): self
    {
        if (!self::isStored($clientId, $session)) {
            throw new RuntimeException('Access Token does not stored.');
        }

        $state = $session->get(StoreKeyGenerator::generate($clientId, self::KEY));

        return self::unserialize(self::decode($state));
    }

    public static function isStored(ClientId $clientId, SessionInterface $session): bool
    {
        return $session->has(StoreKeyGenerator::generate($clientId, self::KEY));
    }

    public static function isStoredAndValid(ClientId $clientId, SessionInterface $session): bool
    {
        if (!self::isStored($clientId, $session)) {
            return false;
        }

        $token = self::get($clientId, $session);

        return $token->isValid();
    }
}
