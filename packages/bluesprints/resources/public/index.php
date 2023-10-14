<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use GuzzleHttp\Psr7\ServerRequest;
use Symfony\Component\Dotenv\Dotenv;
use VerteXVaaR\BlueContainer\DI;
use VerteXVaaR\BlueSprints\Error\ErrorHandler;
use VerteXVaaR\BlueSprints\Http\Application;
use VerteXVaaR\BlueSprints\Http\HttpResponseEmitter;

$root = getenv('VXVR_BS_ROOT');
if (!$root) {
    $root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
    putenv('VXVR_BS_ROOT=' . $root);
}

if (file_exists($root . '/.env')) {
    $dotenv = new Dotenv();
    $dotenv->usePutenv();
    $dotenv->loadEnv($root . '/.env', null, 'dev', [], true);
}

if (empty(ini_get('date.timezone'))) {
    date_default_timezone_set('UTC');
}

if (!class_exists(ClassLoader::class, false)) {
    if (file_exists('../../../vendor/autoload.php')) {
        // project level
        require('../../../vendor/autoload.php');
    } elseif (file_exists('../../../../autoload.php')) {
        // library level
        require('../../../../autoload.php');
    } else {
        throw new Exception('Autoloader not found', 1491561093);
    }
}

$errorHandler = new ErrorHandler();
$errorHandler->register();

$request = ServerRequest::fromGlobals();

$di = new DI();
$response = $di->get(Application::class)->run($request);
$di->get(HttpResponseEmitter::class)->emit($response);
