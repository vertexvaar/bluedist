<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueFoundation\DI;
use VerteXVaaR\BlueWeb\Application;
use VerteXVaaR\BlueWeb\ResponseEmitter;

$request = ServerRequest::fromGlobals();

$di = new DI();
$di->set(ServerRequestInterface::class, $request);

$application = $di->get(Application::class);
$response = $application->run($request);
$responseEmitter = $di->get(ResponseEmitter::class);
$responseEmitter->emit($response);
