<?php
namespace VerteXVaaR\BlueSprints\Controller;

use VerteXVaaR\BlueSprints\Http\Request;
use VerteXVaaR\BlueSprints\Http\Response;
use VerteXVaaR\BlueSprints\View\TemplateRenderer;

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
	 * @var TemplateRenderer
	 */
	protected $templateRenderer = NULL;

	/**
	 * @param Request $request
	 * @param Response $response
	 * @return AbstractController
	 */
	final public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
		$this->templateRenderer = new TemplateRenderer($this->response);
	}

	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param string $url
	 * @return void
	 */
	protected function redirect($url = '') {
		$this->response->setHeader('Location', $url);
	}

}
