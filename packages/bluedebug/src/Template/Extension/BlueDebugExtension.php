<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Template\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

use JsonException;

use function get_object_vars;
use function is_string;
use function json_decode;

class BlueDebugExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            'properties' => new TwigFilter(
                'properties',
                static fn(object $object): array => get_object_vars($object),
            ),
            'json_decode' => new TwigFilter(
                'json_decode',
                static function (string $json): mixed {
                    try {
                        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException) {
                        return null;
                    }
                },
            ),
        ];
    }

    public function getTests(): array
    {
        return [
            'string' => new TwigTest(
                'string',
                static fn(mixed $value): bool => is_string($value),
            ),
        ];
    }
}
