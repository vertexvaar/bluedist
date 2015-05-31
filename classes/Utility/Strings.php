<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Strings
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Strings
{

    /**
     * @return string
     */
    public static function generateUuid()
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
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
     * @return int
     */
    public static function isValidUuid($string)
    {
        return preg_match(
            '~^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$~',
            $string
        );
    }
}
