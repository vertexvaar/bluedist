<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Folders
 */
class Folders
{
    /**
     * @param string $relativeRoot
     * @param string $className
     * @return string
     */
    public static function createFolderForClassName(string $relativeRoot, string $className): string
    {
        $relativePath = $relativeRoot . DIRECTORY_SEPARATOR . self::classNameToFolderName($className);
        $folderName = Files::getAbsoluteFilePath($relativePath);
        if (!is_dir($folderName)) {
            mkdir($folderName, Files::requireFile('configuration/system.php')['permissions']['folders'], true);
        }
        return $relativePath;
    }

    /**
     * @param string $className
     * @return string
     */
    public static function classNameToFolderName(string $className): string
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $className) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $folderName
     * @return array
     */
    public static function getAllFilesInFolder(string $folderName): array
    {
        $files = [];
        /** @var \SplFileInfo[] $fileSystemIterator */
        $fileSystemIterator = new \FilesystemIterator(
            Files::getAbsoluteFilePath($folderName),
            \FilesystemIterator::SKIP_DOTS
        );
        foreach ($fileSystemIterator as $file) {
            $files[] = $file->getPathname();
        }
        return $files;
    }
}
