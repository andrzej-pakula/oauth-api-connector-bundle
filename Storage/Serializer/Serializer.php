<?php

declare(strict_types=1);


namespace Andreo\OAuthClientBundle\Storage\Serializer;


use Andreo\OAuthClientBundle\Storage\StorableInterface;
use Zumba\JsonSerializer\JsonSerializer;

final class Serializer implements SerializerInterface
{
    private JsonSerializer $zumbaSerializer;

    public function __construct(JsonSerializer $zumbaSerializer)
    {
        $this->zumbaSerializer = $zumbaSerializer;
    }

    public function serialize(StorableInterface $storable): string
    {
        return $this->zumbaSerializer->serialize($storable);
    }

    public function deserialize(string $string): StorableInterface
    {
        return $this->zumbaSerializer->unserialize($string);
    }
}
