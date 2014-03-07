<?php
/**
 * @author: Nicolas Levée
 * @version 060320141150
 */

namespace POM;

/**
 * Class IdentityMap
 * @package POM
 */
class IdentityMap implements IdentityMapInterface {

	/**
	 * @var \SplObjectStorage
	 */
	private $_objectToId;

	/**
	 * @var array
	 */
	private $_idToObject;

	/**
	 * Start new IdentityMap
	 */
	public function __construct(){
		$this->_objectToId = new \SplObjectStorage();
		$this->_idToObject = [];
	}

	/**
	 * Supprime l'objet de la table
	 * @param string $key
	 */
	public function removeObject($key) {
		$object = $this->_idToObject[$key];
		unset($this->_objectToId[$object], $this->_idToObject[$key]);
	}

	/**
	 * Stock l'objet
	 * @param string $key
	 * @param mixed $object
	 * @return void
	 */
	public function storeObject($key, $object) {
		$this->_idToObject[$key] = $object;
		$this->_objectToId[$object] = $key;
	}

	/**
	 * Verifie si la clé existe déjà dans le storage
	 * @param string $key
	 * @return bool
	 */
	public function hasId($key) {
		return isset($this->_idToObject[$key]);
	}

	/**
	 * Verifie si l'objet existe déjà dans le storage
	 * @param mixed $object
	 * @return bool
	 */
	public function hasObject($object) {
		return isset($this->_objectToId[$object]);
	}

	/**
	 * Renvoi l'objet associé a la clé ou null si non existant
	 * @param string $key
	 * @return mixed
	 */
	public function getObject($key) {
		if (false === $this->hasId($key)) {
			return null;
		}
		return $this->_idToObject[$key];
	}

	/**
	 * Renvoi l'id associé a un objet dans le storage
	 * @param mixed $object
	 * @return string
	 */
	public function getObjectId($object) {
		if (false === $this->hasObject($object)) {
			return null;
		}
		return $this->_objectToId[$object];
	}
}