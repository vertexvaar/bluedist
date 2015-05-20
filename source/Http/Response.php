<?php
namespace VerteXVaaR\BlueSprints\Http;

/**
 * Class Response
 *
 * @package VerteXVaaR\BlueSprints\Http
 */
class Response {

	/**
	 * @var array
	 */
	protected $headers = [
		'Content-Type' => 'text/html; charset=utf-8',
	];

	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * @return void
	 */
	public function respond() {
		$this->sendHeaders();
		$this->printContent();
	}

	/**
	 * @return void
	 */
	protected function sendHeaders() {
		foreach ($this->headers as $key => $value) {
			header($key . ':' . $value);
		}
	}

	/**
	 * @return void
	 */
	protected function printContent() {
		echo $this->content;
	}

	/**
	 * @param string $content
	 * @return void
	 */
	public function appendContent($content = '') {
		$this->content .= $content;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function setHeader($key = '', $value = '') {
		$this->headers[$key] = $value;
	}
}
