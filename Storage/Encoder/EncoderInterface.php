<?php

declare(strict_types=1);

namespace Andreo\OAuthClientBundle\Storage\Encoder;

interface EncoderInterface
{
    public static function encode(string $string): string;

    public static function decode(string $string): string;
}
