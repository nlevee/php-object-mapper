<?php
/**
 * @author: Nicolas Levée
 * @version 060320141210
 */

namespace POM;

/**
 * Interface CollectionInterface
 * @package POM
 */
interface CollectionInterface extends \Countable, \IteratorAggregate {

	/**
	 * A prototypejs-like pluck function.
	 * @param string $offset
	 * @return DomainObjectInterface[]
	 */
	public function pluck($offset);

}