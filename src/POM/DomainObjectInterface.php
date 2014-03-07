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
	 * Renvoi une copie sous forme de tableau des propriété du model avec leur valeur
	 * si $modified_only est true on renvoi uniquement les valeur modifiers depuis le dernier chargement
	 * @param bool $modified_only
	 * @return array [attribut => 'valeur', ...]
	 */
	public function getArrayCopy($modified_only = false);

	/**
	 * Charge le model a partir d'un tableau de données
	 * @param array $dataset [attribut => 'valeur', ...]
	 * @return void
	 */
	public function populate(array $dataset);

	/**
	 * Valide les donnée de l'objet
	 * @return bool
	 */
	public function validate();

}