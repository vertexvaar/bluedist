<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use VerteXVaaR\BlueSprints\Http;

$requestHandler = new Http\RequestHandler();
$response = $requestHandler->handleRequest(Http\Request::createFromEnvironment());
$response->respond();
