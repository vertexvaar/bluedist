<?php

define('VXVR_BS_ROOT', dirname(realpath(__DIR__)) . DIRECTORY_SEPARATOR);

require_once(VXVR_BS_ROOT . 'vendor/autoload.php');

if (strlen(ini_get('date.timezone')) === 0) {
    date_default_timezone_set('UTC');
}

if (PHP_SAPI === 'cli') {
    $scheduler = new \VerteXVaaR\BlueSprints\Task\Scheduler(
        \VerteXVaaR\BlueSprints\Task\CliRequest::createFromEnvironment()
    );
    $scheduler->run();
} else {
    \VerteXVaaR\BlueSprints\Utility\Error::registerErrorHandler();
    $requestHandler = new \VerteXVaaR\BlueSprints\Http\RequestHandler();
    $response = $requestHandler->handleRequest(\VerteXVaaR\BlueSprints\Http\Request::createFromEnvironment());
    $response->respond();
}
