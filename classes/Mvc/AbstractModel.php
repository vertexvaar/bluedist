<?php
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Files;
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
        $fileContents = Files::readFileContents(self::getFolder() . $uuid);
        if ($fileContents) {
            return unserialize($fileContents);
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
        $files = Folders::getAllFilesInFolder(self::getFolder());
        $results = [];
        foreach ($files as $file) {
            $results[] = unserialize(file_get_contents($file));
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
        $this->checkRequestType();
        if (empty($this->uuid)) {
            $this->uuid = Strings::generateUuid();
        }
        if (empty($this->creationTime)) {
            $this->creationTime = new \DateTime();
        }
        $this->lastModification = new \DateTime();
        Files::writeFileContents(self::getFolder($this) . $this->uuid, serialize($this));
    }

    /**
     * @return void
     * @throws \Exception
     */
    final protected function checkRequestType()
    {
        if (!in_array(VXVR_BS_REQUEST_METHOD, ['PUT', 'POST', 'DELETE'])) {
            throw new \Exception('You may not persist objects in safe requests', 1432469288);
        }
    }
}
