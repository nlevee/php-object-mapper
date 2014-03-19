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
			// on ne sauvegarde les données que si elle sont rempli
			if (!($data = $this->service->fetchOne($query, $condition[1])))
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
		$condition = $this->getEntityCondition($id);
		$query = 'DELETE FROM ' . $this->getEntityTable() . ' WHERE ' . $condition[0];
		if ($this->service->exec($query, $condition[1])) {
			// on supprime l'objet de la map
			$identityKey = implode('-', $id);
			$this->getIdentityMap()->removeObject($identityKey);
			return true;
		}
		return false;
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
		$aEntityList = array_filter($object->getArrayCopy());
		$query = 'INSERT INTO ' . $this->getEntityTable() . ' (`'.implode('`, `', array_keys($aEntityList)).'`) VALUES (:'.implode(', :', array_keys($aEntityList)).')';
		if ($this->service->exec($query, $aEntityList, $insertId)) {
			$this->populate($object, array_combine($this->getEntityPrimaries(), [$insertId]));
			return true;
		}
		return false;
	}

	/**
	 * Update l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * si l'objet n'existe pas déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return mixed
	 */
	public function update(DomainObjectInterface &$object) {
		$aEntityList = array_filter($object->getArrayCopy());
		list($condition, $bindings) = $this->getEntityCondition($aEntityList);
		if (!empty($condition) && !empty($bindings)) {
			$update = [];
			$entities = array_keys(array_diff_key($aEntityList, $bindings));
			foreach ($entities as $key) {
				$placeholder = strtolower($key);
				$update[] = sprintf("`%s` = :$placeholder", $key);
			}
			$query = 'UPDATE ' . $this->getEntityTable() . ' SET ' . implode(', ', $update) . ' WHERE ' . $condition;
			return $this->service->exec($query, $aEntityList);
		}
		return false;
	}

	/**
	 * Supprime l'objet de la DB, si l'objet n'existe pas déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return mixed
	 */
	public function remove(DomainObjectInterface $object) {
		$id = [];
		foreach($this->getEntityPrimaries() as $primaryKey)
			$id[$primaryKey] = $object[$primaryKey];
		return empty($id) ? false : $this->removeById($id);
	}
}