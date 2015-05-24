<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Folders
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Folders
{

    /**
     * @param string $relativeRoot
     * @param string $className
     * @return string
     */
    public static function createFolderForClassName($relativeRoot, $className)
    {
        $configuration = Files::requireFile('configuration/system.php');
        $folderName = VXVR_BS_ROOT .
            $relativeRoot .
            DIRECTORY_SEPARATOR .
            self::classNameToFolderName($className);
        if (!is_dir($folderName)) {
            mkdir($folderName, $configuration['permissions']['folders'], true);
        }
        return $folderName;
    }

    /**
     * @param string $className
     * @return string
     */
    public static function classNameToFolderName($className)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $className) . DIRECTORY_SEPARATOR;
    }
}
