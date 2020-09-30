<?php


namespace Andreo\OAuthApiConnectorBundle\Traits;


use RuntimeException;

trait SerializeTrait
{
    public function serialize(): string
    {
        return serialize($this);
    }

    public static function unserialize(string $str): self
    {
        /** @var self|bool $state */
        $object = unserialize($str, [
            'allowed_classes' => [self::class]
        ]);

        if (false === $object) {
            throw new RuntimeException("Can not unserialize " . self::class . ".");
        }

        return $state;
    }

    private function encode(): string
    {
        return base64_encode($this->serialize());
    }

    private static function decode(string $str): string
    {
        $serialized =  base64_decode($str);
        if (false === $serialized) {
            throw new RuntimeException('Can not decode serialized ' . self::class . ".");
        }

        return $serialized;
    }
}
