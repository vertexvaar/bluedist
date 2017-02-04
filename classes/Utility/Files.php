<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Files
 */
class Files
{
    /**
     * @param string $fileName
     * @return bool
     */
    public static function fileExists(string $fileName): bool
    {
        $absoluteFilePath = self::getAbsoluteFilePath($fileName);
        self::clearStateCache($absoluteFilePath);
        return file_exists($absoluteFilePath);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function getAbsoluteFilePath(string $fileName): string
    {
        return VXVR_BS_ROOT . $fileName;
    }

    /**
     * @param string $absolutePath
     * @return void
     */
    protected static function clearStateCache(string $absolutePath)
    {
        clearstatcache(true, $absolutePath);
    }

    /**
     * @param string $fileName
     * @param array $variables
     * @return mixed
     * @throws \Exception
     */
    public static function requireOnceFile(string $fileName, array $variables = [])
    {
        $absoluteFilePath = self::getAbsoluteFilePath($fileName);
        if (!is_file($absoluteFilePath)) {
            throw new \Exception(
                'Error: require_once(' . htmlspecialchars($absoluteFilePath) . '): failed to open stream: ' .
                'No such file or directory in ' . __FILE__ . ' on line ' . __LINE__,
                1432841751
            );
        }
        foreach ($variables as $variableName => $variable) {
            $$variableName = $variable;
        }
        return require_once($absoluteFilePath);
    }

    /**
     * @param string $fileName
     * @param array $variables
     * @return mixed
     * @throws \Exception
     */
    public static function requireFile(string $fileName, array $variables = [])
    {
        $absoluteFilePath = self::getAbsoluteFilePath($fileName);
        if (!is_file($absoluteFilePath)) {
            throw new \Exception(
                'Error: require(' . htmlspecialchars($absoluteFilePath) . '): failed to open stream: ' .
                'No such file or directory in ' . __FILE__ . ' on line ' . __LINE__,
                1432841754
            );
        }
        foreach ($variables as $variableName => $variable) {
            $$variableName = $variable;
        }
        return require($absoluteFilePath);
    }

    /**
     * @param string $fileName
     * @param string $fileContents
     * @return void
     */
    public static function writeFileContents(string $fileName, string $fileContents)
    {
        file_put_contents(self::getAbsoluteFilePath($fileName), $fileContents);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function readFileContents(string $fileName): string
    {
        return file_get_contents(self::getAbsoluteFilePath($fileName));
    }

    /**
     * @param string $fileName
     * @param string $fileContents
     * @return int
     */
    public static function touch(string $fileName, string $fileContents = '')
    {
        $absolutePath = self::getAbsoluteFilePath($fileName);
        if (!is_file($absolutePath)) {
            return file_put_contents($absolutePath, $fileContents);
        }
        return 0;
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public static function delete(string $fileName): bool
    {
        return unlink(self::getAbsoluteFilePath($fileName));
    }
}
