<?php

use VerteXVaaR\BlueSprints\Http\RequestInterface;
use VerteXVaaR\BlueWelcome\Controller\Welcome;

return [
	// safe methods
	RequestInterface::HTTP_METHOD_GET => [
		'/updateFruit' => [
			'controller' => Welcome::class,
			'action' => 'updateFruit',
		],
		'/editFruit' => [
			'controller' => Welcome::class,
			'action' => 'editFruit'
		],
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
		'/updateFruit' => [
			'controller' => Welcome::class,
			'action' => 'updateFruit',
		],
		'/createFruit' => [
			'controller' => Welcome::class,
			'action' => 'createFruit',
		],
	],
	RequestInterface::HTTP_METHOD_PUT => [],
	RequestInterface::HTTP_METHOD_DELETE => [],
];
