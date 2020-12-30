<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\GitHub\User;

use Andreo\OAuthClientBundle\User\UserInterface;

final class User implements UserInterface
{
    private string $id;

    private ?string $email;

    public function __construct(string $id, ?string $email = null)
    {
        $this->id = $id;
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
