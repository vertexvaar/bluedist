<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Utility;

class Context
{
    public const CONTEXT_PRODUCTION = 'Production';
    public const CONTEXT_DEVELOPMENT = 'Development';

    protected string $context;

    public function __construct()
    {
        $this->context = Files::requireFile('config/system.php')['context'] ?? self::CONTEXT_PRODUCTION;
    }

    public function getCurrentContext(): string
    {
        return $this->context;
    }
}
