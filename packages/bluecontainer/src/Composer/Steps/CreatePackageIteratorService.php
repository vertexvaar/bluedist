<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Composer\Steps;

use Composer\Package\PackageInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueContainer\Helper\PackageIterator;
use VerteXVaaR\BlueContainer\Service\DependencyOrderingService;

use function array_column;
use function array_combine;
use function array_key_exists;
use function array_map;

readonly class CreatePackageIteratorService implements Step
{
    public function run(ContainerBuilder $container): void
    {
        $composer = $container->get('composer');

        $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();
        $packages[] = $composer->getPackage();

        // Index packages by their name for easier access
        $packages = array_combine(
            array_map(static fn(PackageInterface $package): string => $package->getName(), $packages),
            $packages,
        );

        $packagesWithDependencies = [];
        foreach ($packages as $name => $package) {
            $after = [];
            foreach ($package->getRequires() as $requireLink) {
                $target = $requireLink->getTarget();
                if (array_key_exists($target, $packages)) {
                    $after[] = $target;
                }
            }
            $packagesWithDependencies[$name] = [
                'package' => $package,
                'after' => $after,
            ];
        }

        $dependencyOrderingService = new DependencyOrderingService();
        $packagesOrderedByDependency = $dependencyOrderingService->orderByDependencies($packagesWithDependencies);
        $packagesOrderedByDependency = array_column($packagesOrderedByDependency, 'package');

        $container->set('package_iterator', new PackageIterator($packagesOrderedByDependency));
    }
}
