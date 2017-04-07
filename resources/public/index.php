<?php

if (!defined('VXVR_BS_ROOT')) {
    define('VXVR_BS_ROOT', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
}

if (!class_exists(\Composer\Autoload\ClassLoader::class, false)) {
    if (file_exists('../../../vendor/autoload.php')) {
        // project level
        require('../../../vendor/autoload.php');
    } elseif (file_exists('../../../../autoload.php')) {
        // library level
        require('../../../../autoload.php');
    } else {
        throw new \Exception('Autoloader not found', 1491561093);
    }
}
if (empty(ini_get('date.timezone'))) {
    date_default_timezone_set('UTC');
}

\VerteXVaaR\BlueSprints\Utility\Error::registerErrorHandler();
(new \VerteXVaaR\BlueSprints\Http\Request())->process()->respond();
