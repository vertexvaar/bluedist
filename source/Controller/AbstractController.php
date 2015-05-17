<?php
namespace VerteXVaaR\BlueSprints\Controller;

use VerteXVaaR\BlueSprints\Http\Request;
use VerteXVaaR\BlueSprints\Http\Response;

/**
 * Class AbstractController
 *
 * @package VerteXVaaR\BlueSprints\Controller
 */
abstract class AbstractController {

	/**
	 * @var Request
	 */
	protected $request = NULL;

	/**
	 * @var Response
	 */
	protected $response = NULL;

	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @param Request $request
	 * @return AbstractController
	 */
	public function setRequest($request) {
		$this->request = $request;
		return $this;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param Response $response
	 * @return AbstractController
	 */
	public function setResponse($response) {
		$this->response = $response;
		return $this;
	}
}
