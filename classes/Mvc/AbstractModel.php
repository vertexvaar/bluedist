<?php
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Folders;
use VerteXVaaR\BlueSprints\Utility\Strings;

/**
 * Class AbstractModel
 *
 * @package VerteXVaaR\BlueSprints\Model
 */
class AbstractModel
{

    /**
     * @var string[]
     */
    static private $classFolders = [];

    /**
     * @var string
     */
    protected $uuid = '';

    /**
     * @var \DateTime
     */
    protected $creationTime = null;

    /**
     * @var \DateTime
     */
    protected $lastModification = null;

    /**
     * @param $uuid
     * @return $this
     */
    final public static function findByUuid($uuid)
    {
        $fileName = self::getFolder() . $uuid;
        if (file_exists($fileName)) {
            return unserialize(file_get_contents($fileName));
        }
        return null;
    }

    /**
     * @param AbstractModel $object
     * @return string
     */
    final protected static function getFolder(AbstractModel $object = null)
    {
        if ($object !== null) {
            $className = get_class($object);
        } else {
            $className = get_called_class();
        }
        if (!array_key_exists($className, self::$classFolders)) {
            self::$classFolders[$className] = Folders::createFolderForClassName('database', $className);
        }
        return self::$classFolders[$className];
    }

    final public static function findAll()
    {
        $results = [];
        /** @var \SplFileInfo[] $fileSystemIterator */
        $fileSystemIterator = new \FilesystemIterator(self::getFolder(), \FilesystemIterator::SKIP_DOTS);
        foreach ($fileSystemIterator as $file) {
            $results[] = unserialize(file_get_contents($file->getPathname()));
        }
        return $results;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return \DateTime
     */
    public function getLastModification()
    {
        return $this->lastModification;
    }

    /**
     * @return void
     */
    final public function save()
    {
        if (empty($this->uuid)) {
            $this->uuid = Strings::generateUuid();
        }
        if (empty($this->creationTime)) {
            $this->creationTime = new \DateTime();
        }
        $this->lastModification = new \DateTime();
        file_put_contents(self::getFolder($this) . $this->uuid, serialize($this));
    }
}
