<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use Closure;
use DateTime;
use DateTimeImmutable;
use VerteXVaaR\BlueSprints\Paths;
use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;
use VerteXVaaR\BlueSprints\Utility\Strings;

use function array_key_exists;
use function basename;
use function file_get_contents;
use function serialize;
use function unserialize;

use const DIRECTORY_SEPARATOR;

class Repository
{
    public function __construct(private readonly Paths $paths)
    {
    }

    public function findByUuid(string $uuid, string $className): ?Entity
    {
        $databaseFolder = Folders::createFolderForClassName($this->paths->database, $className);
        $fileContents = Files::readFileContents($databaseFolder . DIRECTORY_SEPARATOR . $uuid);
        if ($fileContents) {
            return unserialize(
                $fileContents,
                ['allowed_classes' => [$className, DateTime::class, DateTimeImmutable::class]]
            );
        }
        return null;
    }

    public function findAll(string $className): array
    {
        $databaseFolder = Folders::createFolderForClassName($this->paths->database, $className);
        $files = Folders::getAllFilesInFolder($databaseFolder);
        $results = [];
        foreach ($files as $file) {
            if (Strings::isValidUuid(basename($file))) {
                $results[] = unserialize(
                    file_get_contents($file),
                    ['allowed_classes' => [$className, DateTime::class, DateTimeImmutable::class]]
                );
            }
        }
        return $results;
    }

    public function persist(Entity $entity): void
    {
        $uuid = $entity->getUuid();
        if (null === $uuid) {
            $uuid = Strings::generateUuid();
            $entity->setUuid($uuid);
        }

        $className = $entity::class;
        $databaseFolder = Folders::createFolderForClassName($this->paths->database, $className);

        $indexColumns = $entity->getIndexColumns();
        if ([] !== $indexColumns) {
            $indicesFile = $databaseFolder . DIRECTORY_SEPARATOR . 'Indices';
            Files::touch($indicesFile, serialize([]));

            $indices = unserialize(
                Files::readFileContents($indicesFile),
                ['allowed_classes' => []]
            );
            if (array_key_exists($uuid, $indices)) {
                $indexEntry = $indices[$uuid];
            } else {
                $indexEntry = [];
            }
            $getter = Closure::bind(fn(string $property): mixed => $this->{$property}, $entity, $entity);
            foreach ($indexColumns as $columnName) {
                $indexEntry[$columnName] = $getter($columnName);
            }
            $indices[$uuid] = $indexEntry;
            Files::writeFileContents($indicesFile, serialize($indices));
        }

        Files::writeFileContents($databaseFolder . DIRECTORY_SEPARATOR . $uuid, serialize($entity));
    }

    public function delete(Entity $entity): void
    {
        $uuid = $entity->getUuid();
        if (null === $uuid) {
            // Entity does not exist, no nee to delete it
            return;
        }
        $className = $entity::class;
        $databaseFolder = Folders::createFolderForClassName($this->paths->database, $className);
        Files::delete($databaseFolder . DIRECTORY_SEPARATOR . $entity->getUuid());
    }
}
