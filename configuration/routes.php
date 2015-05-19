<?php

use VerteXVaaR\BlueSprints\Controller\Frontend;
use VerteXVaaR\BlueSprints\Http\RequestInterface;

return [
	// safe methods
	RequestInterface::HTTP_METHOD_GET => [
		'/hello' => [
			'controller' => Frontend::class,
			'action' => 'hello',
		],
		'.*' => [
			'controller' => Frontend::class,
			'action' => 'show',
		],
	],
	RequestInterface::HTTP_METHOD_HEAD => [],
	// not safe methods
	RequestInterface::HTTP_METHOD_POST => [],
	RequestInterface::HTTP_METHOD_PUT => [],
	RequestInterface::HTTP_METHOD_DELETE => [],
];
