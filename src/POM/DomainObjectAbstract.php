<?php
/**
 * @author: Nicolas Levée
 * @version 060320141650
 */

namespace POM;

/**
 * Class DomainObjectAbstract
 * @package POM
 */
abstract class DomainObjectAbstract implements DomainObjectInterface {

	/**
	 * @var array
	 */
	private $_editableProperties;


	/**
	 * Renvoi la liste des propriétés editable,
	 * par default les propriétés protected sont toutes editable
	 * @return array
	 */
	public function getEditableProperties() {
		return $this->_editableProperties ?: ($this->_editableProperties = array_map(function($property){
			return $property->getName();
		}, (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED)));
	}

	/**
	 * Charge le model a partir d'un tableau de données
	 * @param array $dataset [attribut => 'valeur', ...]
	 * @throws \LogicException
	 * @return void
	 */
	public function initFromArray(array $dataset) {
		if (!empty($dataset)) {
			foreach($this->getEditableProperties() as $propName){
				if (isset($dataset[$propName]))
					$this[$propName] = $dataset[$propName];
			}
		}
	}

	/**
	 * Renvoi une copie sous forme de tableau des propriété du model avec leur valeur
	 * si $modified_only est true on renvoi uniquement les valeur modifiers depuis le dernier chargement
	 * @param bool $modified_only
	 * @return array [attribut => 'valeur', ...]
	 */
	public function getArrayCopy($modified_only = false) {
		$aToArrayCopy = [];
		foreach($this->getEditableProperties() as $propName){
			$aToArrayCopy[$propName] = $this[$propName];
		}
		return $aToArrayCopy;
	}


	/**
	 * Permet de valider l'intégrité du model
	 * @return bool
	 */
	public function validate() {
		return true;
	}


	/**
	 * Permet la verification d'une propriété via l'objet comme si les attribut était public :
	 *	isset($model->attribut);
	 * @param string $offset
	 * @return bool|void
	 */
	public function __isset($offset) {
		return $this->offsetExists($offset);
	}

	/**
	 * permet l'edition de propriétés via l'objet comme si les attribut était public :
	 * 	$model->attribut = 'new value';
	 * @param string $offset
	 * @param mixed $value
	 */
	public function __set($offset, $value) {
		$this->offsetSet($offset, $value);
	}

	/**
	 * permet l'accès aux propriétés via l'objet comme si les attribut était public :
	 * 	echo $model->attribut;
	 * @param string $offset
	 * @return mixed|void
	 */
	public function __get($offset) {
		return $this->offsetGet($offset);
	}

	/**
	 * Permet la suppression d'une propriété via l'objet comme si les attribut était public :
	 *	unset($model->attribut);
	 * @param string $offset
	 */
	public function __unset($offset) {
		$this->offsetUnset($offset);
	}


	/**
	 * @see http://www.php.net/manual/fr/arrayaccess.offsetexists.php
	 * @param string $offset
	 * @return bool|void
	 */
	public function offsetExists($offset) {
		return in_array($offset, $this->_editableProperties);
	}

	/**
	 * @see http://www.php.net/manual/fr/arrayaccess.offsetget.php
	 * @param string $offset
	 * @return mixed|void
	 */
	public function offsetGet($offset) {
		if ($this->offsetExists($offset))
			return $this->$offset;
		trigger_error("La propriété $offset n'existe pas");
		return null;
	}

	/**
	 * @see http://www.php.net/manual/fr/arrayaccess.offsetset.php
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		if ($this->offsetExists($offset))
			$this->$offset = $value;
		else
			trigger_error("La propriété $offset n'existe pas");
	}

	/**
	 * @see http://www.php.net/manual/fr/arrayaccess.offsetunset.php
	 * @param string $offset
	 */
	public function offsetUnset($offset) {
		if ($this->offsetExists($offset))
			$this->$offset = null;
	}


	/**
	 * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return \Traversable
	 */
	public function getIterator() {
		return new \ArrayIterator($this->getArrayCopy());
	}

}