<?php
/**
 * @author: Nicolas Levée
 * @version 060320141701
 */

namespace POM;

/**
 * Class CollectionAbstract
 * @package POM
 */
abstract class CollectionAbstract extends \ArrayObject implements CollectionInterface {

	/**
	 * @var string
	 */
	protected $modelClassName = '\POM\DomainObjectInterface';


	/**
	 * @param string $domainObjectClass
	 * @throws \InvalidArgumentException
	 */
	public function __construct($domainObjectClass = '\POM\DomainObjectInterface') {
		if (class_exists($domainObjectClass)) {
			$this->modelClassName = $domainObjectClass;
		} else {
			throw new \InvalidArgumentException("'$domainObjectClass' is not a valid class name");
		}
	}

	/**
	 * A prototypejs-like pluck function.
	 * @param string $offset
	 * @return DomainObjectInterface[]
	 */
	public function pluck($offset) {
		$array = array();
		foreach ($this as $model) {
			if (isset($model->{$offset})) {
				$array[] = $model->{$offset};
			}
		}
		return $array;
	}

	/**
	 * Overridden method to ensure that only the correct type of models are added.
	 * @see \ArrayObject::offsetSet()
	 * @param mixed $offset
	 * @param mixed $newval
	 */
	public function offsetSet($offset, $newval) {
		$this->checkType($newval);
		parent::offsetSet($offset, $newval);
	}

	/**
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	protected function checkType($value) {
		if (!is_a($value, $this->modelClassName)) {
			throw new \InvalidArgumentException('Provided object is not an instance of ' . $this->modelClassName);
		}
	}

}