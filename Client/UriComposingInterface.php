<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Client;


interface UriComposingInterface
{
    public function addHTTPParameter(AggregateHTTPParamInterface $httpParam): self;

    public function getUri(): string;
}
