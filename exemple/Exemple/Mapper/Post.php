<?php
/**
 * @author: Nicolas Levée
 * @version 070320141535
 */

namespace Exemple\Mapper;

use POM\DomainObjectInterface;
use POM\Service\Mysql\MapperAbstract;
use POM\Service\Mysql\Adapter;

/**
 * Class Post
 * @package Exemple\Mapper
 */
class Post extends MapperAbstract {

	/**
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter) {
		parent::__construct($adapter, 'post', ['id']);
	}

	/**
	 * @see MapperAbstract::insert()
	 */
	public function insert(DomainObjectInterface &$object) {
		$object['date_create'] = new \DateTime();
		$object['date_update'] = new \DateTime();
		return parent::insert($object);
	}

	/**
	 * @see MapperAbstract::update()
	 */
	public function update(DomainObjectInterface &$object) {
		$object['date_update'] = new \DateTime();
		return parent::update($object);
	}

}