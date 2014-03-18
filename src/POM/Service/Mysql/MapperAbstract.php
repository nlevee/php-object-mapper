<?php
/**
 * @author: Nicolas Levée
 * @version 060320141837
 */

namespace POM\Service\Mysql;

use POM\DomainObjectInterface;

/**
 * Class MapperAbstract
 * @package POM\Service\Mysql
 */
abstract class MapperAbstract extends \POM\MapperAbstract {

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
	 * Returns the SQL condition to identity this model
	 * @param array $entities
	 * @return array (SQL_WHERE, BINDINGS)
	 */
	private function getEntityCondition(array $entities) {
		$bindings = $conditions = array();
		foreach ($entities as $key => $value) {
			if (in_array($key, $this->getEntityPrimaries())) {
				$placeholder = strtolower($key);
				$conditions[] = sprintf("`%s` = :$placeholder", $key);
				$bindings[$placeholder] = $value;
			}
		}
		$sql = implode(' AND ', $conditions);
		return array($sql, $bindings);
	}

	/**
	 * Charge les données dans $object de l'element trouvé via son id,
	 * renvoi true/false selon la réussite de la requete
	 * @param mixed $id
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function fetchById($id, DomainObjectInterface &$object) {
		if (!is_array($id))
			$id = array_combine($this->getEntityPrimaries(), [$id]);
		// on verifie que la map ne possede pas déja la clé
		$identityKey = implode('-', $id);
		if (!$this->getIdentityMap()->hasId($identityKey)) {
			$condition = $this->getEntityCondition($id);
			$query = 'SELECT * FROM ' . $this->getEntityTable() . ' WHERE ' . $condition[0];
			$sth = $this->service->prepare($query);
			$sth->execute($condition[1]);
			// on ne sauvegarde les données que si elle sont rempli
			if (!($data = $sth->fetch()))
				return false;
			$this->populate($object, $data);
			$this->getIdentityMap()->storeObject($identityKey, $object);
		} else {
			$object = $this->getIdentityMap()->getObject($identityKey);
		}
		return true;
	}

	/**
	 * Supprime un element de la DB via son ID,
	 * renvoi true/false selon la réussite de la requete
	 * @param mixed $id
	 * @return bool
	 */
	public function removeById($id) {
		if (!is_array($id))
			$id = array_combine($this->getEntityPrimaries(), [$id]);
		// on verifie que la map ne possede pas déja la clé
		$identityKey = implode('-', $id);
		$this->getIdentityMap()->removeObject($identityKey);
		$condition = $this->getEntityCondition($id);
		$query = 'DELETE FROM ' . $this->getEntityTable() . ' WHERE ' . $condition[0];
		$sth = $this->service->prepare($query);
		return $sth->execute($condition[1]);
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

	}
}