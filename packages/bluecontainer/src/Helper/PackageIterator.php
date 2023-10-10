<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Helper;

use Closure;
use Composer\Composer;

use function array_reverse;

readonly class PackageIterator
{
    public function __construct(private Composer $composer)
    {
    }

    /**
     * Iterates over all installed packages, passing the package to the closure
     * and collecting the return values in an array which is returned.
     */
    public function iterate(Closure $closure): array
    {
        $return = [];
        $installationManager = $this->composer->getInstallationManager();
        $packages = $this->composer->getRepositoryManager()->getLocalRepository()->getPackages();
        foreach (array_reverse($packages) as $package) {
            $installPath = $installationManager->getInstallPath($package);
            $return[] = $closure($package, $installPath);
        }
        return $return;
    }
}
