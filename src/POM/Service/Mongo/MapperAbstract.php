<?php
/**
 * @author: Nicolas Levée
 * @version 240320141048
 */

namespace POM\Service\Mongo;

use POM\DomainObjectInterface;

/**
 * Class MapperAbstract
 * @package POM\Service\Mongo
 */
class MapperAbstract extends \POM\MapperAbstract {

	/**
	 * @var Adapter
	 */
	protected $service;

	/**
	 * @param Adapter $service
	 * @param string $entityTable
	 * @param array|string $entityPrimaries
	 */
	public function __construct(Adapter $service, $entityTable, $entityPrimaries) {
		parent::__construct($service, $entityTable, $entityPrimaries);
	}

	/**
	 * Charge les données dans $object de l'element trouvé via son id,
	 * renvoi true/false selon la réussite de la requete
	 * @param mixed $id
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function fetchById($id, DomainObjectInterface &$object) {
		// TODO: Implement fetchById() method.
	}

	/**
	 * Supprime un element de la DB via son ID,
	 * renvoi true/false selon la réussite de la requete
	 * @param mixed $id
	 * @return bool
	 */
	public function removeById($id) {
		// TODO: Implement removeById() method.
	}

	/**
	 * Sauvegarde l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function save(DomainObjectInterface &$object) {
		// TODO: Implement save() method.
	}

	/**
	 * Insert l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * si l'objet existe déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function insert(DomainObjectInterface &$object) {
		// TODO: Implement insert() method.
	}

	/**
	 * Update l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * si l'objet n'existe pas déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return mixed
	 */
	public function update(DomainObjectInterface &$object) {
		// TODO: Implement update() method.
	}

	/**
	 * Supprime l'objet de la DB, si l'objet n'existe pas déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return mixed
	 */
	public function remove(DomainObjectInterface $object) {
		// TODO: Implement remove() method.
	}
}