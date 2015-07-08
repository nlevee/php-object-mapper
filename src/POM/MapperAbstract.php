<?php
/**
 * @author: Nicolas Levée
 * @version 060320141634
 */

namespace POM;

use POM\Service\AdapterInterface;

/**
 * Class MapperAbstract
 * @package POM
 */
abstract class MapperAbstract implements MapperInterface {

	/**
	 * @var AdapterInterface
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
	 * @param AdapterInterface $service
	 * @param string $entityTable
	 * @param array|string $entityPrimaries
	 */
	public function __construct(AdapterInterface $service, $entityTable, $entityPrimaries) {
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
		return $this->entityPrimaries;
	}

	/**
	 * @return AdapterInterface
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
		$object->populate($data);
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