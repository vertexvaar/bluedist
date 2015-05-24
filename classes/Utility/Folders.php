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
        $relativePath = $relativeRoot .
            DIRECTORY_SEPARATOR .
            self::classNameToFolderName($className);
        $folderName = VXVR_BS_ROOT . $relativePath;
        if (!is_dir($folderName)) {
            mkdir($folderName, $configuration['permissions']['folders'], true);
        }
        return $relativePath;
    }

    /**
     * @param string $className
     * @return string
     */
    public static function classNameToFolderName($className)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $className) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $folderName
     * @return string[]
     */
    public static function getAllFilesInFolder($folderName)
    {
        $files = [];
        /** @var \SplFileInfo[] $fileSystemIterator */
        $fileSystemIterator = new \FilesystemIterator(VXVR_BS_ROOT . $folderName, \FilesystemIterator::SKIP_DOTS);
        foreach ($fileSystemIterator as $file) {
            $files[] = $file->getPathname();
        }
        return $files;
    }
}
