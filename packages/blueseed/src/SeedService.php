<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSeed;

use Exception;
use VerteXVaaR\BlueSeed\Seeder\Seeder;

use function sprintf;

readonly class SeedService
{
    /** @var array<Seeder> */
    protected(set) array $seeders;

    /**
     * @param iterable<Seeder> $seeders
     */
    public function __construct(
        iterable $seeders,
    ) {
        $indexedSeeders = [];
        foreach ($seeders as $seeder) {
            $indexedSeeders[$seeder::class] = $seeder;
        }
        $this->seeders = $indexedSeeders;
    }

    public function seed(string $name): void
    {
        $seeders = $this->seeders;
        if (!isset($seeders[$name])) {
            throw new Exception(sprintf('Seeder "%s" not found', $name));
        }
        foreach ($seeders[$name]->getDependencies() as $dependency) {
            $this->seed($dependency);
        }
        $seeders[$name]->seed();
    }

    public function seedAll(): void
    {
        foreach ($this->seeders as $seeder) {
            $seeder->seed();
        }
    }
}
