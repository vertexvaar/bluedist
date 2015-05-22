<?php

use VerteXVaaR\BlueSprints\Http\RequestInterface;
use VerteXVaaR\BlueWelcome\Controller\Welcome;

return [
	// safe methods
	RequestInterface::HTTP_METHOD_GET => [
		'/listFruits' => [
			'controller' => Welcome::class,
			'action' => 'listFruits',
		],
		'.*' => [
			'controller' => Welcome::class,
			'action' => 'index',
		],
	],
	RequestInterface::HTTP_METHOD_HEAD => [],
	// not safe methods
	RequestInterface::HTTP_METHOD_POST => [
		'/createFruit' => [
			'controller' => Welcome::class,
			'action' => 'createFruit',
		],
	],
	RequestInterface::HTTP_METHOD_PUT => [],
	RequestInterface::HTTP_METHOD_DELETE => [],
];
