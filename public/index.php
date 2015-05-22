<?php
define('VXVR_BS_ROOT', dirname(dirname(realpath(__FILE__))) . DIRECTORY_SEPARATOR);

require_once(__DIR__ . '/../vendor/autoload.php');

use VerteXVaaR\BlueSprints\Http;

$requestHandler = new Http\RequestHandler();
$response = $requestHandler->handleRequest(Http\Request::createFromEnvironment());
$response->respond();
