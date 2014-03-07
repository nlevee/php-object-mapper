<?php
/**
 * @author: Nicolas Levée
 * @version 060320141144
 */

namespace POM;

/**
 * Interface DomainObjectInterface
 * @package POM
 */
interface DomainObjectInterface extends \ArrayAccess, \IteratorAggregate {

	/**
	 * Renvoi une copie sous forme de tableau de l'objet
	 * @return array
	 */
	public function getArrayCopy();

	/**
	 * Charge les données d'un tableau dans l'objet
	 * @param array $data
	 * @return void
	 */
	public function loadFromArray(array $data);

	/**
	 * Valide les donnée de l'objet
	 * @return bool
	 */
	public function validate();

}