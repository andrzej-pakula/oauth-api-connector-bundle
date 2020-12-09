<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage;

use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Exception\ExpiredAccessTokenException;
use Andreo\OAuthClientBundle\Exception\StorableNotExistException;
use Andreo\OAuthClientBundle\Storage\Encoder\EncoderInterface;
use Andreo\OAuthClientBundle\Storage\Serializer\SerializerInterface;
use DateTimeImmutable;

final class SessionStorage implements StorageInterface
{
    private EncoderInterface $encoder;

    private SerializerInterface $serializer;

    public function __construct(EncoderInterface $encoder, SerializerInterface $serializer)
    {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
    }

    public function store(HttpContext $httpContext, string $key, StorableInterface $storable): void
    {
        $request = $httpContext->getRequest();
        $string = $this->encoder::encode($this->serializer->serialize($storable));
        $request->getSession()->set($key, $string);
    }

    public function restore(HttpContext $httpContext, string $key): StorableInterface
    {
        if (!$this->has($httpContext, $key)) {
            throw new StorableNotExistException($key);
        }

        $request = $httpContext->getRequest();

        $string = $request->getSession()->get($key);

        $storable = $this->serializer->deserialize($this->encoder::decode($string));
        if ($storable->getExpiredAt() <= new DateTimeImmutable()) {
            $this->delete($httpContext, $key);

            throw new ExpiredAccessTokenException($storable);
        }

        return $storable;
    }

    public function has(HttpContext $httpContext, string $key): bool
    {
        $request = $httpContext->getRequest();

        return $request->getSession()->has($key);
    }

    public function delete(HttpContext $httpContext, string $key): void
    {
        if (!$this->has($httpContext, $key)) {
            throw new StorableNotExistException($key);
        }

        $httpContext->getSession()->remove($key);
    }
}
