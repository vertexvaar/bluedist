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
		$this->templateRenderer->setVariable('foo', ['bar', 'baz']);
		$this->templateRenderer->setVariable('pageTitle', 'myFrontend');
		$this->templateRenderer->render('Frontend/Show');
	}

	/**
	 * @return void
	 */
	public function hello() {
		$this->templateRenderer->setVariable('name', $this->request->getArgument('name'));
		$this->templateRenderer->render('Frontend/Hello');
	}

}
