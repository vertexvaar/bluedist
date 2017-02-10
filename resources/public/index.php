<?php

define('VXVR_BS_APP', dirname(realpath(__DIR__)) . DIRECTORY_SEPARATOR);
define('VXVR_BS_CONFIG', VXVR_BS_APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
define('VXVR_BS_ROOT', dirname(VXVR_BS_APP) . DIRECTORY_SEPARATOR);

require_once(VXVR_BS_ROOT . 'vendor/autoload.php');

if (empty(ini_get('date.timezone'))) {
    date_default_timezone_set('UTC');
}

\VerteXVaaR\BlueSprints\Utility\Error::registerErrorHandler();
(new \VerteXVaaR\BlueSprints\Http\Request())->process()->respond();
