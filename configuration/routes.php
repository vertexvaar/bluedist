<?php

use VerteXVaaR\BlueSprints\Http\RequestInterface;

return array(
	// safe methods
	RequestInterface::HTTP_METHOD_GET => array(
		'.*' => array(
			'controller' => '\\VerteXVaaR\\BlueSprints\\Controller\\Frontend',
			'action' => 'show'
		),
	),
	RequestInterface::HTTP_METHOD_HEAD => array(),
	// not safe methods
	RequestInterface::HTTP_METHOD_POST => array(),
	RequestInterface::HTTP_METHOD_PUT => array(),
	RequestInterface::HTTP_METHOD_DELETE => array(),
);
