<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Utility;

use FilesystemIterator;
use SplFileInfo;

class Folders
{
    public static function createFolderForClassName(string $relativeRoot, string $className): string
    {
        $relativePath = $relativeRoot . DIRECTORY_SEPARATOR . self::classNameToFolderName($className);
        self::createFolderRecursive($relativePath);
        return $relativePath;
    }

    public static function classNameToFolderName(string $className): string
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $className) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $folder
     * @return bool If the folder exists or was created
     */
    public static function createFolderRecursive(string $folder): bool
    {
        $absolutePath = Files::getAbsoluteFilePath($folder);
        if (!is_dir($absolutePath)) {
            return mkdir(
                $absolutePath,
                Files::requireFile('config/system.php')['permissions']['folders'],
                true
            );
        }
        return true;
    }
}
