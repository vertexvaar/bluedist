<?php

use VerteXVaaR\BlueSprints\Http\RequestInterface;

return [
	// safe methods
	RequestInterface::HTTP_METHOD_GET => [
		'/hello' => [
			'controller' => '\\VerteXVaaR\\BlueSprints\\Controller\\Frontend',
			'action' => 'hello',
		],
		'.*' => [
			'controller' => '\\VerteXVaaR\\BlueSprints\\Controller\\Frontend',
			'action' => 'show',
		],
	],
	RequestInterface::HTTP_METHOD_HEAD => [],
	// not safe methods
	RequestInterface::HTTP_METHOD_POST => [],
	RequestInterface::HTTP_METHOD_PUT => [],
	RequestInterface::HTTP_METHOD_DELETE => [],
];
