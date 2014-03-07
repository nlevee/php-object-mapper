<?php
/**
 * @author: Nicolas Levée
 * @version 060320141145
 */

namespace POM;

/**
 * Interface IdentityMapInterface
 * @package POM
 */
interface IdentityMapInterface {

	/**
	 * Stock l'objet
	 * @param string $key
	 * @param mixed$object
	 * @return void
	 */
	public function storeObject($key, $object);

	/**
	 * Supprime l'objet de la table
	 * @param string $key
	 */
	public function removeObject($key);

	/**
	 * Verifie si la clé existe déjà dans le storage
	 * @param string $key
	 * @return bool
	 */
	public function hasId($key);

	/**
	 * Verifie si l'objet existe déjà dans le storage
	 * @param mixed $object
	 * @return bool
	 */
	public function hasObject($object);

	/**
	 * Renvoi l'objet associé a la clé ou null si non existant
	 * @param string $key
	 * @return mixed
	 */
	public function getObject($key);

	/**
	 * Renvoi l'id associé a un objet dans le storage
	 * @param mixed $object
	 * @return string
	 */
	public function getObjectId($object);

}