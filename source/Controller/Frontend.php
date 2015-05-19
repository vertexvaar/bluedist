<?php
namespace VerteXVaaR\BlueSprints\Controller;

use VerteXVaaR\BlueSprints\Model\Person;

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

	/**
	 * @return void
	 */
	public function listPerson() {
		$this->templateRenderer->setVariable('persons', Person::findAll());
		$this->templateRenderer->render('Frontend/ListPersons');
	}

	/**
	 * @return void
	 */
	public function newPerson() {
		$this->templateRenderer->render('Frontend/NewPerson');
	}

	/**
	 * @return void
	 */
	public function createPerson() {
		$person = new Person();
		$person->setFirstName($this->request->getArgument('firstName'));
		$person->setLastName($this->request->getArgument('lastName'));
		$person->save();
		$this->redirect('listPerson');
	}

	/**
	 * @return void
	 */
	public function showPerson() {
		$this->templateRenderer->setVariable(
			'person',
			Person::findByUuid($this->request->getArgument('person'))
		);
		$this->templateRenderer->render('Frontend/ShowPerson');
	}
}
