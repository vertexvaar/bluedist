<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Utility;

class Strings
{
    private const UUID_PATTERN = '~^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$~';
    private const UUID_FORMAT = '%04X%04X-%04X-%04X-%04X-%04X%04X%04X';

    public static function generateUuid(): string
    {
        return sprintf(
            static::UUID_FORMAT,
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isValidUuid(string $string): bool
    {
        return 1 === preg_match(static::UUID_PATTERN, $string);
    }
}
