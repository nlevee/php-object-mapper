<?php
/**
 * @author: Nicolas Levée
 * @version 060320141634
 */

namespace POM\Mapper;

use POM\DomainObject\DomainObjectInterface;
use POM\IdentityMap;
use POM\IdentityMapInterface;
use POM\Services\ServiceAdapterInterface;

/**
 * Class DataMapperAbstract
 * @package POM\Mapper
 */
abstract class DataMapperAbstract implements DataMapperInterface {

	/**
	 * @var ServiceAdapterInterface
	 */
	protected $service;

	/**
	 * @var IdentityMapInterface
	 */
	protected $identityMap;

	/**
	 * @var string
	 */
	protected $entityTable;

	/**
	 * @var array
	 */
	protected $entityPrimaries;


	/**
	 * @param ServiceAdapterInterface $service
	 * @param string $entityTable
	 * @param array|string $entityPrimaries
	 */
	public function __construct(ServiceAdapterInterface $service, $entityTable, $entityPrimaries) {
		$this->service = $service;
		$this->entityTable = $entityTable;
		if (!is_array($entityPrimaries))
			$entityPrimaries = array($entityPrimaries);
		$this->entityPrimaries = $entityPrimaries;
		// creation de l'entityMap utilisable pour la creation de table d'entité
		$this->identityMap = new IdentityMap();
	}


	/**
	 * @return string
	 */
	public function getEntityTable() {
		return $this->entityTable;
	}

	/**
	 * @return array
	 */
	public function getEntityPrimaries() {
		return $this->$entityPrimaries;
	}

	/**
	 * @return ServiceAdapterInterface
	 */
	public function getService() {
		return $this->service;
	}


	/**
	 * charge les donnée $data dans l'objet $object
	 * @param DomainObjectInterface $object
	 * @param array $data
	 */
	public function populate(DomainObjectInterface &$object, array $data) {
		foreach($data as $k=>$v) {
			if (isset($object[$k]))
				$object[$k] = $v;
		}
	}

	/**
	 * @return IdentityMapInterface
	 */
	public function getIdentityMap() {
		return $this->identityMap;
	}

	/**
	 * @param IdentityMapInterface $identityMap
	 */
	public function setIdentityMap($identityMap) {
		$this->identityMap = $identityMap;
	}

}