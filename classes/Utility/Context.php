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
     * @return string
     */
    public static function getCurrentContext(): string
    {
        return Files::requireFile('app/config/system.php')['context'];
    }
}
