<?php
namespace VerteXVaaR\BlueSprints\Model;

/**
 * Class Person
 *
 * @package VerteXVaaR\BlueSprints\Model
 */
class Person extends AbstractModel {

	/**
	 * @var array
	 */
	static public $columns = [
		'firstName' => 'VARCHAR(255) DEFAULT "" NOT NULL',
		'lastName' => 'VARCHAR(255) DEFAULT "" NOT NULL',
	];

	/**
	 * @var string
	 */
	protected $firstName = '';

	/**
	 * @var string
	 */
	protected $lastName = '';

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 * @return Person
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 * @return Person
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFullName() {
		return $this->firstName . ' ' . $this->lastName;
	}
}
