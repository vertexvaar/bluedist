<?php
namespace VerteXVaaR\BlueSprints\Controller;

/**
 * Class Frontend
 *
 * @package VerteXVaaR\BlueSprints\Controller
 */
class Frontend extends AbstractController {

	/**
	 * @return void
	 */
	public function show() {
		$this->response->appendContent('Simple Message');
	}

}
