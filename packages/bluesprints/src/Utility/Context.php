<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Context
 */
class Context
{
    const CONTEXT_PRODUCTION = 'Production';
    const CONTEXT_DEVELOPMENT = 'Development';

    /**
     * @var string
     */
    protected $context = self::CONTEXT_PRODUCTION;

    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->context = Files::requireFile('config/system.php')['context'];
    }

    /**
     * @return string
     */
    public function getCurrentContext(): string
    {
        return $this->context;
    }
}
