<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints;

readonly class Config
{
    public function __construct(
        public int $filePermissions,
        public int $folderPermissions,
    ) {
    }
}
