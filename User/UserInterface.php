<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\User;

interface UserInterface
{
    public function getId(): string;

    public function getEmail(): ?string;
}
