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
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

use function CoStack\Lib\concat_paths;
use function file_exists;
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

    public function findByIdentifier(string $class, string $identifier): ?object
    {
        $databaseFolder = $this->getFolder($class);
        $databaseFile = concat_paths($databaseFolder, $identifier);
        if (!file_exists($databaseFile)) {
            return null;
        }
        $fileContents = file_get_contents($databaseFile);
        return unserialize(
            $fileContents,
            ['allowed_classes' => [$class, DateTime::class, DateTimeImmutable::class]],
        );
    }

    public function findAll(string $class): array
    {
        $databaseFolder = $this->getFolder($class);

        $results = [];
        /** @var SplFileInfo[] $fileSystemIterator */
        $fileSystemIterator = new FilesystemIterator($databaseFolder, FilesystemIterator::SKIP_DOTS);
        foreach ($fileSystemIterator as $file) {
            $results[] = unserialize(
                file_get_contents($file->getPathname()),
                ['allowed_classes' => [$class, DateTime::class, DateTimeImmutable::class]],
            );
        }
        return $results;
    }

    public function store(Entity $entity): void
    {
        $identifier = $entity->identifier;
        $databaseFolder = $this->getFolder($entity::class);
        file_put_contents(concat_paths($databaseFolder, $identifier), serialize($entity));
    }

    public function delete(Entity $entity): void
    {
        $databaseFolder = $this->getFolder($entity::class);
        $entityFile = concat_paths($databaseFolder, $entity->identifier);
        if (file_exists($entityFile)) {
            unlink($entityFile);
        }
    }

    private function getFolder(string $class): string
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
