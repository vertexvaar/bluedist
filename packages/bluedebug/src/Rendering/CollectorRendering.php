<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Rendering;

readonly class CollectorRendering
{
    public function __construct(
        public string $title,
        public string $shortInformation,
        public ?array $popupTable = null,
    ) {
    }
}
