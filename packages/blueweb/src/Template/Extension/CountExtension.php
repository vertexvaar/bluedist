<?php

namespace VerteXVaaR\BlueWeb\Template\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CountExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            'count' => new TwigFilter(
                'count',
                static fn(mixed $value): int => count($value),
            ),
        ];
    }
}
