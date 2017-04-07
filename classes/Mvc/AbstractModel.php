<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;
use VerteXVaaR\BlueSprints\Utility\Strings;

/**
 * Class AbstractModel
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
     * @param string $uuid
     * @return $this|null
     */
    final public static function findByUuid(string $uuid)
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
    final protected static function getFolder(AbstractModel $object = null): string
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

    /**
     * @return $this[]
     */
    final public static function findAll(): array
    {
        $files = Folders::getAllFilesInFolder(self::getFolder());
        $results = [];
        foreach ($files as $file) {
            if (Strings::isValidUuid(basename($file))) {
                $results[] = unserialize(file_get_contents($file));
            }
        }
        return $results;
    }

    /**
     * @param string $property
     * @param string $value
     * @return $this[]
     */
    final public static function findByProperty(string $property, string $value): array
    {
        $indicesFile = self::getFolder() . 'Indices';
        $indices = unserialize(Files::readFileContents($indicesFile));
        $results = [];
        foreach ($indices as $uuid => $index) {
            if ($index[$property] === $value) {
                $results[] = self::findByUuid($uuid);
            }
        }
        return $results;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime(): \DateTime
    {
        return $this->creationTime;
    }

    /**
     * @return \DateTime
     */
    public function getLastModification(): \DateTime
    {
        return $this->lastModification;
    }

    /**
     * @param bool $force Do not validate if the Request is considered safe
     */
    final public function save(bool $force = false)
    {
        $this->checkRequestType($force);
        if (empty($this->uuid)) {
            $this->uuid = Strings::generateUuid();
        }
        if (empty($this->creationTime)) {
            $this->creationTime = new \DateTime();
        }
        $this->lastModification = new \DateTime();
        $this->updateIndices();
        Files::writeFileContents(self::getFolder($this) . $this->uuid, serialize($this));
    }

    /**
     * @param bool $force
     */
    final public function delete(bool $force = false)
    {
        $this->checkRequestType($force);
        Files::delete(self::getFolder($this) . $this->uuid);
    }

    /**
     * Regenerates the indices file with updated object properties
     */
    final protected function updateIndices()
    {
        if (!empty($this->getIndexColumns())) {
            $indicesFile = self::getFolder($this) . 'Indices';
            Files::touch($indicesFile, serialize([]));
            $indices = unserialize(Files::readFileContents($indicesFile));
            if (array_key_exists($this->uuid, $indices)) {
                $indexEntry = $indices[$this->uuid];
            } else {
                $indexEntry = [];
            }
            foreach ($this->getIndexColumns() as $columnName) {
                $indexEntry[$columnName] = $this->{$columnName};
            }
            $indices[$this->uuid] = $indexEntry;
            Files::writeFileContents($indicesFile, serialize($indices));
        }
    }

    /**
     * @param bool $force
     * @throws \Exception
     */
    final protected function checkRequestType(bool $force = false)
    {
        if ($force !== true) {
            if (!in_array(VXVR_BS_REQUEST_METHOD, ['PUT', 'POST', 'DELETE'])) {
                throw new \Exception('You may not persist objects in safe requests', 1432469288);
            }
        }
    }

    /**
     * @return array
     */
    protected function getIndexColumns(): array
    {
        return [];
    }
}
