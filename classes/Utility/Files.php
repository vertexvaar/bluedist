<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Files
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Files
{

    /**
     * @param string $fileName
     * @return bool
     */
    public static function fileExists($fileName = '')
    {
        $absoluteFilePath = self::getAbsoluteFilePath($fileName);
        self::clearStateCache($absoluteFilePath);
        return file_exists($absoluteFilePath);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function getAbsoluteFilePath($fileName = '')
    {
        return VXVR_BS_ROOT . $fileName;
    }

    /**
     * @param string $absolutePath
     * @return void
     */
    protected static function clearStateCache($absolutePath = '')
    {
        clearstatcache(true, $absolutePath);
    }

    /**
     * @param string $fileName
     * @param array $variables
     * @return mixed
     */
    public static function requireOnceFile($fileName = '', array $variables = [])
    {
        $absoluteFilePath = self::getAbsoluteFilePath($fileName);
        foreach ($variables as $variableName => $variable) {
            $$variableName = $variable;
        }
        return require_once($absoluteFilePath);
    }

    /**
     * @param string $fileName
     * @param array $variables
     * @return mixed
     */
    public static function requireFile($fileName = '', array $variables = [])
    {
        $absoluteFilePath = self::getAbsoluteFilePath($fileName);
        foreach ($variables as $variableName => $variable) {
            $$variableName = $variable;
        }
        return require($absoluteFilePath);
    }
}
