<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Context
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Context
{

    const CONTEXT_PRODUCTION = 'Production';

    const CONTEXT_DEVELOPMENT = 'Development';

    /**
     * @return mixed
     */
    public static function getCurrentContext()
    {
        return Files::requireFile('configuration/system.php')['context'];
    }

}
