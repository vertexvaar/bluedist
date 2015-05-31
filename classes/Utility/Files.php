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
     * @throws \Exception
     */
    public static function requireOnceFile($fileName = '', array $variables = [])
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
    public static function requireFile($fileName = '', array $variables = [])
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
    public static function writeFileContents($fileName, $fileContents)
    {
        file_put_contents(self::getAbsoluteFilePath($fileName), $fileContents);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function readFileContents($fileName)
    {
        return file_get_contents(self::getAbsoluteFilePath($fileName));
    }

    /**
     * @param string $fileName
     * @param string $fileContents
     * @return int
     */
    public static function touch($fileName, $fileContents = '')
    {
        $absolutePath = self::getAbsoluteFilePath($fileName);
        if (!is_file($absolutePath)) {
            return file_put_contents($absolutePath, $fileContents);
        }
        return 0;
    }
}
