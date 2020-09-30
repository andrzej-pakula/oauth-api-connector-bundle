<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\AccessToken;


use Andreo\OAuthApiConnectorBundle\Client\ClientId;
use Andreo\OAuthApiConnectorBundle\Traits\SerializeTrait;
use Andreo\OAuthApiConnectorBundle\Util\KeyGenerator;
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
        $session->set(KeyGenerator::generate($clientId, self::KEY), $this->encode());
    }

    public static function getFromStorage(ClientId $clientId, SessionInterface $session): self
    {
        if (!self::inStorage($clientId, $session)) {
            throw new RuntimeException('Access Token does not exist in storage.');
        }

        $state = $session->get(KeyGenerator::generate($clientId, self::KEY));

        return self::unserialize(self::decode($state));
    }

    public static function inStorage(ClientId $clientId, SessionInterface $session): bool
    {
        return $session->has(KeyGenerator::generate($clientId, self::KEY));
    }

    public static function existAndValid(ClientId $clientId, SessionInterface $session): bool
    {
        if (!self::inStorage($clientId, $session)) {
            return false;
        }

        $token = self::getFromStorage($clientId, $session);

        return $token->isValid();
    }
}
