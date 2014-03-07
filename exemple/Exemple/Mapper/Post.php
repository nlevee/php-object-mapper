<?php
/**
 * @author: Nicolas Levée
 * @version 070320141535
 */

namespace Exemple\Mapper;

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

}