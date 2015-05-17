<?php
namespace VerteXVaaR\BlueSprints\Utility;

/**
 * Class Files
 *
 * @package VerteXVaaR\BlueSprints\Utility
 */
class Files {

	/**
	 * @param string $fileName
	 * @return bool
	 */
	static public function fileExists($fileName = '') {
		$absoluteFilePath = self::getAbsoluteFilePath($fileName);
		self::clearStateCache($absoluteFilePath);
		return file_exists($absoluteFilePath);
	}

	/**
	 * @param string $fileName
	 * @return mixed
	 */
	static public function requireOnceFile($fileName = '', $variables = array()) {
		$absoluteFilePath = self::getAbsoluteFilePath($fileName);
		foreach ($variables as $variableName => $variable) {
			$$variableName = $variable;
		}
		return require_once($absoluteFilePath);
	}

	/**
	 * @param string $fileName
	 * @return mixed
	 */
	static public function requireFile($fileName = '', $variables = array()) {
		$absoluteFilePath = self::getAbsoluteFilePath($fileName);
		foreach ($variables as $variableName => $variable) {
			$$variableName = $variable;
		}
		return require($absoluteFilePath);
	}

	/**
	 * @param string $fileName
	 * @return string
	 */
	static public function getAbsoluteFilePath($fileName = '') {
		return Environment::getDocumentRoot() . $fileName;
	}

	/**
	 * @param string $absolutePath
	 * @return void
	 */
	static protected function clearStateCache($absolutePath = '') {
		clearstatcache(TRUE, $absolutePath);
	}
}
