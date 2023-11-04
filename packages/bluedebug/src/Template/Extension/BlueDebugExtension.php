<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Template\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use function get_object_vars;

class BlueDebugExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            'properties' => new TwigFilter(
                'properties',
                static fn(object $object): array => get_object_vars($object),
            ),
        ];
    }
}
