<?php


namespace Andreo\OAuthClientBundle\Storage;


use Andreo\OAuthClientBundle\Client\RequestContext\ClientId;
use RuntimeException;

trait EncodingTrait
{
    public function encrypt(): string
    {
        return base64_encode($this->serialize());
    }

    private function serialize(): string
    {
        return serialize($this);
    }

    public static function decrypt(string $str): self
    {
        $serialized = base64_decode($str);

        if (false === $serialized) {
            throw new RuntimeException('Can not decode serialized ' . self::class . ".");
        }

        return self::unserialize($serialized);
    }

    private static function unserialize(string $str): self
    {
        /** @var self|bool $object */
        $object = unserialize($str, [
            'allowed_classes' => [self::class]
        ]);

        if (false === $object) {
            throw new RuntimeException("Can not unserialize " . self::class . ".");
        }

        return $object;
    }

    public static function getKey(ClientId $clientId): string
    {
        return StoreKeyGenerator::generate($clientId, self::KEY);
    }
}
