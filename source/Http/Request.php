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
	protected $path = '';

	/**
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * @return Request
	 */
	static public function createFromEnvironment() {
		$request = new self;
		$request->setMethod($_SERVER['REQUEST_METHOD']);
		$request->setPath(explode('?', $_SERVER['REQUEST_URI'])[0]);
		foreach ($_REQUEST as &$value) {
			$value = htmlspecialchars($value);
		}
		$request->setArguments($_REQUEST);
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
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return Request
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * @param array $arguments
	 * @return Request
	 */
	public function setArguments($arguments) {
		$this->arguments = $arguments;
		return $this;
	}

	/**
	 * @param $key
	 * @return string
	 */
	public function getArgument($key) {
		return $this->arguments[$key];
	}
}
