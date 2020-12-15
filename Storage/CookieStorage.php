<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage;

use Andreo\OAuthClientBundle\Client\HttpContext;
use Andreo\OAuthClientBundle\Exception\ObjectExpiredException;
use Andreo\OAuthClientBundle\Exception\StorableNotExistException;
use Andreo\OAuthClientBundle\Storage\Encoder\EncoderInterface;
use Andreo\OAuthClientBundle\Storage\Serializer\SerializerInterface;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Cookie;

final class CookieStorage implements StorageInterface
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
        $cookie = Cookie::create($key)
            ->withSecure(true)
            ->withValue($this->encoder::encode($this->serializer->serialize($storable)));

        if ($storable instanceof ThisIsExpiringInterface) {
            $cookie->withExpires($storable->getExpiredAt());
        }

        $response = $httpContext->getResponse();

        $response->headers->setCookie($cookie);
    }

    public function restore(HttpContext $httpContext, string $key): StorableInterface
    {
        if (!$this->has($httpContext, $key)) {
            throw new StorableNotExistException($key);
        }
        $request = $httpContext->getRequest();

        /** @var string $string */
        $string = $request->cookies->get($key);
        $storable = $this->serializer->deserialize($this->encoder::decode($string));
        if (!$storable instanceof ThisIsExpiringInterface) {
            return $storable;
        }
        if ($storable->getExpiredAt() <= new DateTimeImmutable()) {
            $this->delete($httpContext, $key);

            throw new ObjectExpiredException($storable);
        }

        return $storable;
    }

    public function has(HttpContext $httpContext, string $key): bool
    {
        $request = $httpContext->getRequest();

        return $request->cookies->has($key);
    }

    public function delete(HttpContext $httpContext, string $key): void
    {
        if (!$this->has($httpContext, $key)) {
            throw new StorableNotExistException($key);
        }

        $httpContext->getRequest()->cookies->remove($key);
    }
}
