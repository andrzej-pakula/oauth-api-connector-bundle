<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Storage\Serializer;



use Andreo\OAuthClientBundle\Storage\StorableInterface;

interface SerializerInterface
{
    public function serialize(StorableInterface $storable): string;

    public function deserialize(string $string): StorableInterface;
}
