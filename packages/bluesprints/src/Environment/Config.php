<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Environment;

readonly class Config
{
    public function __construct(
        public int $filePermissions,
        public int $folderPermissions,
        public string $cookieDomain,
        public string $cookieAuthName,
    ) {
    }
}
