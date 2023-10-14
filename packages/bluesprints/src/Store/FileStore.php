<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Store;

use DateTime;
use DateTimeImmutable;
use FilesystemIterator;
use RuntimeException;
use SplFileInfo;
use VerteXVaaR\BlueSprints\Environment\Config;
use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Mvc\Entity;
use VerteXVaaR\BlueSprints\Utility\Strings;

use function CoStack\Lib\concat_paths;
use function file_get_contents;
use function getenv;
use function is_dir;
use function mkdir;
use function serialize;
use function str_replace;
use function unserialize;

use const DIRECTORY_SEPARATOR as DS;

readonly class FileStore implements Store
{
    public function __construct(private Paths $paths, private Config $config)
    {
    }

    public function findByUuid(string $class, string $uuid): ?object
    {
        $databaseFolder = $this->getFolder($class);
        $fileContents = file_get_contents(concat_paths($databaseFolder, $uuid));
        if ($fileContents) {
            return unserialize(
                $fileContents,
                ['allowed_classes' => [$class, DateTime::class, DateTimeImmutable::class]]
            );
        }
        return null;
    }

    public function findAll(string $class): array
    {
        $databaseFolder = $this->getFolder($class);

        $results = [];
        /** @var SplFileInfo[] $fileSystemIterator */
        $fileSystemIterator = new FilesystemIterator($databaseFolder, FilesystemIterator::SKIP_DOTS);
        foreach ($fileSystemIterator as $file) {
            if (Strings::isValidUuid($file->getBasename())) {
                $results[] = unserialize(
                    file_get_contents($file->getPathname()),
                    ['allowed_classes' => [$class, DateTime::class, DateTimeImmutable::class]]
                );
            }
        }
        return $results;
    }

    public function store(Entity $entity): void
    {
        $uuid = $entity->uuid;
        $databaseFolder = $this->getFolder($entity::class);
        file_put_contents(concat_paths($databaseFolder, $uuid), serialize($entity));
    }

    public function delete(Entity $entity): void
    {
        $databaseFolder = $this->getFolder($entity::class);
        unlink(concat_paths($databaseFolder, $entity->uuid));
    }

    public function getFolder(string $class): string
    {
        $classFolder = concat_paths(getenv('VXVR_BS_ROOT'), $this->paths->database, str_replace('\\', DS, $class));

        if (
            !is_dir($classFolder)
            && !mkdir($classFolder, $this->config->folderPermissions, true)
            && !is_dir($classFolder)
        ) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $classFolder));
        }

        return $classFolder;
    }
}
