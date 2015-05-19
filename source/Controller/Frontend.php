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
		if ($this->request->hasArgument('name')) {
			$name = $this->request->getArgument('name');
		} else {
			$name = '[noname]';
		}
		$this->templateRenderer->setVariable('name', $name);
		$this->templateRenderer->render('Frontend/Hello');
	}

}
