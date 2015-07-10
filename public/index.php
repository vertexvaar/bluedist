<?php

define('VXVR_BS_ROOT', dirname(dirname(realpath(__FILE__))) . DIRECTORY_SEPARATOR);

require_once(__DIR__ . '/../vendor/autoload.php');

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
