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
     * @param string $className
     * @return string
     */
    public static function createFolderForClassName($className = '')
    {
        $configuration = Files::requireFile('configuration/system.php');
        $folderName = Environment::getDocumentRoot() .
            'database' .
            DIRECTORY_SEPARATOR .
            str_replace('\\', DIRECTORY_SEPARATOR, $className) .
            DIRECTORY_SEPARATOR;
        if (!is_dir($folderName)) {
            mkdir($folderName, $configuration['permissions']['folders'], true);
        }
        return $folderName;
    }
}
