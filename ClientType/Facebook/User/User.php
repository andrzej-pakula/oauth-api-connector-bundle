<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\ClientType\Facebook\User;

use Andreo\OAuthClientBundle\User\UserInterface;

final class User implements UserInterface
{
    private string $id;

    private string $firstName;

    private string $lastName;

    private ?string $email;

    public function __construct(string $id, string $firstName, string $lastName, ?string $email = null)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
