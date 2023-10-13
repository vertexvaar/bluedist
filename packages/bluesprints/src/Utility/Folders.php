<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Utility;

use FilesystemIterator;
use JetBrains\PhpStorm\Pure;
use SplFileInfo;

use function CoStack\Lib\concat_paths;
use function str_replace;
use function strtr;

class Folders
{
    public static function createFolderForClassName(string $relativeRoot, string $className): string
    {
        $relativePath = concat_paths($relativeRoot, self::classNameToFolderName($className));
        self::createFolderRecursive($relativePath);
        return $relativePath;
    }

    #[Pure]
    public static function classNameToFolderName(string $className): string
    {
        return strtr($className, '\\', '/') . '/';
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
