<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Environment
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Environment
{

    /**
     * @return string
     */
    public static function getDocumentRoot()
    {
        return dirname(dirname(dirname(realpath(__FILE__)))) . DIRECTORY_SEPARATOR;
    }

}
