<?php

declare(strict_types=1);


namespace Andreo\OAuthApiConnectorBundle\Security;


interface StateGeneratorInterface
{
    public function generate(): string;
}