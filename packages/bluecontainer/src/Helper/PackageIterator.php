<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Helper;

use Closure;

use function array_merge_recursive;
use function is_array;

readonly class PackageIterator
{
    public function __construct(private array $packages)
    {
    }

    /**
     * Iterates over all installed packages, passing the package to the closure
     * and collecting the return values in an array which is returned.
     */
    public function map(Closure $closure): array
    {
        $return = [];
        foreach ($this->packages as $package) {
            $closureResult = $closure($package);
            if (is_array($closureResult)) {
                $return[] = $closureResult;
            }
        }
        return array_merge_recursive([], ...$return);
    }

    public function iterate(Closure $closure): void
    {
        foreach ($this->packages as $package) {
            $closure($package);
        }
    }
}
