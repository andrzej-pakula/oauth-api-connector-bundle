<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Client;

interface UriComposingInterface
{
    public function addHttpParameter(HttpParameterInterface $httpParam): self;

    public function getUri(): string;
}
