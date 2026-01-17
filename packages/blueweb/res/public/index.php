<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueFoundation\Generated\DI;
use VerteXVaaR\BlueWeb\Application;
use VerteXVaaR\BlueWeb\ErrorHandler\ErrorHandler;
use VerteXVaaR\BlueWeb\ResponseEmitter;

use function CoStack\Lib\concat_paths;

$root = getenv('VXVR_BS_ROOT');
if (!$root) {
    $root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
    putenv('VXVR_BS_ROOT=' . $root);
}

include concat_paths($root, 'dotenv.php');

if (empty(ini_get('date.timezone'))) {
    date_default_timezone_set('UTC');
}

$errorHandler = new ErrorHandler();
$errorHandler->register();

$request = ServerRequest::fromGlobals();

$di = new DI();
$di->set(ServerRequestInterface::class, $request);

$application = $di->get(Application::class);
$response = $application->run($request);
$responseEmitter = $di->get(ResponseEmitter::class);
$responseEmitter->emit($response);
