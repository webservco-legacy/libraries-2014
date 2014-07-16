<?php

abstract class Library {

	/**
	 * Stores object instances.
	 *
	 * @var array
	 */
	protected static $_msInstance = array();

	/**
	 * Get object instance.
	 *
	 * @param string $className name of the class to instantiate
	 *
	 * @return object
	 */
	protected static function _GetInstance($className) {
		/**
		 * Check if requested instance is the base library.
		 */
		if (__CLASS__ == $className) {
			/**
			 * Stop.
			 */
			return FALSE;
		}

		/**
		 * Check that instance is not already set.
		 */
		if (!isset(self::$_msInstance[$className])) {
			/**
			 * Set object instance.
			 */
			self::$_msInstance[$className] = new $className();
		}

		/**
		 * Return object instance.
		 */
		return self::$_msInstance[$className];
	}

}
?>