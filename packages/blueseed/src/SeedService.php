<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSeed;

use InvalidArgumentException;
use RuntimeException;
use VerteXVaaR\BlueFoundation\Service\DependencyOrderingService;
use VerteXVaaR\BlueSeed\Exception\SeederNotFoundException;
use VerteXVaaR\BlueSeed\Seeder\Seeder;

use function array_column;
use function array_keys;
use function implode;
use function sprintf;

readonly class SeedService
{
    /** @var array<string, array{'seeder': Seeder, 'after': array<string>}> */
    protected(set) array $seeders;

    /**
     * @param iterable<Seeder> $seeders
     * @throws SeederNotFoundException
     */
    public function __construct(iterable $seeders)
    {
        $indexedSeeders = [];
        foreach ($seeders as $seeder) {
            $indexedSeeders[$seeder->getIdentifier()] = [
                'seeder' => $seeder,
                'after' => $seeder->getDependencies(),
            ];
        }
        foreach ($indexedSeeders as $identifier => $seederData) {
            foreach ($seederData['after'] as $dependency) {
                if (!isset($indexedSeeders[$dependency])) {
                    throw new SeederNotFoundException($identifier, $dependency);
                }
            }
        }
        $this->seeders = $indexedSeeders;
    }

    public function seedAll(): void
    {
        $seeders = $this->orderDependencies($this->seeders);

        foreach ($seeders as $seeder) {
            $seeder->seed();
        }
    }

    public function seed(string $identifier): void
    {
        if (!isset($this->seeders[$identifier])) {
            throw new InvalidArgumentException(
                sprintf('Seeder "%s" does not exist.', $identifier),
            );
        }

        $seeders = $this->collectDependencies($identifier);

        $seeders = $this->orderDependencies($seeders);

        foreach ($seeders as $seeder) {
            $seeder->seed();
        }
    }

    /**
     * @param array<string, array{'seeder': Seeder, 'after': array<string>}> $seeders
     * @return array<string, Seeder>
     */
    protected function orderDependencies(array $seeders): array
    {
        $dependencyOrderingService = new DependencyOrderingService();
        $seeders = $dependencyOrderingService->orderByDependencies($seeders);
        return array_column($seeders, 'seeder');
    }

    /**
     * @param string $identifier
     * @param array<string, true> $visiting
     * @return array<string, array{'seeder': Seeder, 'after': array<string>}>
     */
    protected function collectDependencies(string $identifier, array $visiting = []): array
    {
        if (isset($visiting[$identifier])) {
            throw new RuntimeException(
                sprintf(
                    'Circular dependency detected: %s',
                    implode(' -> ', array_keys($visiting)) . ' -> ' . $identifier,
                ),
            );
        }

        $seederData = $this->seeders[$identifier];
        $visiting[$identifier] = true;
        $result = [$identifier => $seederData];

        foreach ($seederData['seeder']->getDependencies() as $dependency) {
            foreach ($this->collectDependencies($dependency, $visiting) as $childIdentifier => $childSeederData) {
                $result[$childIdentifier] = $childSeederData;
            }
        }

        return $result;
    }
}
