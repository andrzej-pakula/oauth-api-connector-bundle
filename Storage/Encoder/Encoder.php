<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage\Encoder;

final class Encoder implements EncoderInterface
{
    public static function encode(string $string): string
    {
        return base64_encode($string);
    }

    public static function decode(string $string): string
    {
        return base64_decode($string, true);
    }
}
