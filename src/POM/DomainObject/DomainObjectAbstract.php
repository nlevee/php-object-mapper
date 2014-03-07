<?php
/**
 * @author: Nicolas Levée
 * @version 060320141650
 */

namespace POM\DomainObject;

/**
 * Class DomainObjectAbstract
 * @package POM\DomainObject
 */
abstract class DomainObjectAbstract extends \ArrayObject implements DomainObjectInterface {

	/**
	 * Chargement
	 */
	public function __construct() {
		// on charge l'objet a vide
		parent::__construct([], \ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * Charge les données d'un tableau dans l'objet
	 * @param array $data
	 * @return void
	 */
	public function loadFromArray(array $data) {
		$this->exchangeArray($data);
	}

}