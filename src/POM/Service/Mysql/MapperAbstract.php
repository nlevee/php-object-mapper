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
	 * @return Adapter
	 */
	public function getService() {
		return parent::getService();
	}


	/**
	 * Returns the SQL condition to identify this model
	 * @param array $entities
	 * @return array (SQL_WHERE, BINDINGS, IDMAP)
	 */
	final protected function getEntityCondition(array $entities) {
		$primaries = $this->getEntityPrimaries();
		$entitiesFiltered = array_intersect_key($entities, array_flip($primaries));
		$condition = $this->getCondition($entitiesFiltered);
		array_push($condition, implode('-', $entitiesFiltered));
		return $condition;
	}

	/**
	 * Returns the SQL condition
	 * @param array $entities
	 * @param string $glue default ' AND '
	 * @return array (SQL_WHERE, BINDINGS)
	 */
	final protected function getCondition(array $entities, $glue = ' AND ') {
		$service = $this->getService();

		// generation des valeur modifié
		$values = array_map(function ($value) use ($service) {
			if (is_array($value)) {
				$value = implode(',', array_map(function ($value) use ($service) {
					return is_numeric($value) ? $value : $service->quoteString($value);
				}, $value));
			}
			return $value;
		}, $entities);

		// generation des placeholders
		$placeholders = array_map(function ($key) {
			return strtolower(str_replace('.', '_', $key));
		}, array_keys($entities));

		// generation du ntableau de binding
		$bindings = array_combine($placeholders, $values);

		// gestion de la chaine de conditions
		$conditions = array_map(function ($key, $placeholder, $value) {
			if (is_array($value))
				return sprintf("`%s` IN (:$placeholder)", $key);
			else
				return sprintf("`%s` = :$placeholder", (string)$key);
		}, array_keys($entities), $placeholders, $values);

		$sql = implode($glue, $conditions);
		ksort($bindings);
		return [$sql, $bindings];
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
		list($where, $bindings, $identityKey) = $this->getEntityCondition($id);
		// on verifie que la map ne possede pas déja la clé
		if (!$this->getIdentityMap()->hasId($identityKey)) {
			$query = 'SELECT * FROM `' . $this->getEntityTable() . '` WHERE ' . $where;
			// on ne sauvegarde les données que si elle sont rempli
			if (!($data = $this->service->fetchOne($query, $bindings)))
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
		list($where, $bindings, $identityKey) = $this->getEntityCondition($id);
		$query = 'DELETE FROM `' . $this->getEntityTable() . '` WHERE ' . $where;
		if ($this->service->exec($query, $bindings)) {
			// on supprime l'objet de la map
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
		$aEntityList = array_filter($object->getArrayCopy());
		list(, $bindings) = $this->getEntityCondition($aEntityList);
		if (!empty($bindings)) {
			$query = 'REPLACE INTO `' . $this->getEntityTable() . '` (`' . implode('`, `', array_keys($aEntityList)) . '`) VALUES (:' . implode(', :', array_keys($aEntityList)) . ')';
			return $this->service->exec($query, $aEntityList);
		}
		return $this->insert($object);
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
		$query = 'INSERT INTO `' . $this->getEntityTable() . '` (`' . implode('`, `', array_keys($aEntityList)) . '`) VALUES (:' . implode(', :', array_keys($aEntityList)) . ')';
		if ($this->service->exec($query, $aEntityList, $insertId)) {
			$this->populate($object, array_combine($this->getEntityPrimaries(), [$insertId]));
			// insert en identityMap
			list(, , $identityKey) = $this->getEntityCondition($object->getArrayCopy());
			$this->getIdentityMap()->storeObject($identityKey, $object);
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
		list($condition, $bindings, $identityKey) = $this->getEntityCondition($aEntityList);
		if (!empty($condition) && !empty($bindings)) {
			$primaries = $this->getEntityPrimaries();
			$entities = array_diff_key($aEntityList, array_flip($primaries));
			list($update, $updateBindings) = $this->getCondition($entities, ', ');
			$query = 'UPDATE `' . $this->getEntityTable() . '` SET ' . $update . ' WHERE ' . $condition;
			if ($this->service->exec($query, array_merge($updateBindings, $bindings)) > 0) {
				// insert en identityMap
				$this->getIdentityMap()->storeObject($identityKey, $object);
				return true;
			}
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
		foreach ($this->getEntityPrimaries() as $primaryKey)
			$id[$primaryKey] = $object[$primaryKey];
		return empty($id) ? false : $this->removeById($id);
	}
}