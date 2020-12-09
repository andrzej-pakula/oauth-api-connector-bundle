<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Storage;


use Andreo\OAuthClientBundle\Client\HttpContext;

interface StorageInterface
{
    public function store(HttpContext $httpContext, string $key, StorableInterface $storable): void;

    public function restore(HttpContext $httpContext, string $key): StorableInterface;

    public function delete(HttpContext $httpContext, string $key): void;

    public function has(HttpContext $httpContext, string $key): bool;
}
