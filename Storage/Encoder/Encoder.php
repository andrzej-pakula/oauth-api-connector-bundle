<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage\Encoder;

final class Encoder implements EncoderInterface
{
    public static function encode(string $string): string
    {
        return bin2hex($string);
    }

    public static function decode(string $string): string
    {
        return hex2bin($string);
    }
}
