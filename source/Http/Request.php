<?php
namespace VerteXVaaR\BlueSprints\Http;

/**
 * Class Request
 *
 * @package VerteXVaaR\BlueSprints\Http
 */
class Request {

	/**
	 * @var string
	 */
	protected $method = '';

	/**
	 * @var string
	 */
	protected $requestUri = '';

	/**
	 * @return Request
	 */
	static public function createFromEnvironment() {
		$request = new self;
		$request->setMethod($_SERVER['REQUEST_METHOD']);
		$request->setRequestUri($_SERVER['REQUEST_URI']);
		return $request;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return Request
	 */
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRequestUri() {
		return $this->requestUri;
	}

	/**
	 * @param string $requestUri
	 * @return Request
	 */
	public function setRequestUri($requestUri) {
		$this->requestUri = $requestUri;
		return $this;
	}
}
