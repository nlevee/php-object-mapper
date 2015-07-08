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
	private $_editableTypesProperties;


	/**
	 * Renvoi la liste des propriétés editable,
	 * par default les propriétés protected sont toutes editable
	 * @return array
	 */
	public function getEditableProperties() {
		return array_keys($this->getEditableTypesProperties());
	}

	/**
	 * Renvoi la liste des type pour les valeur de properties,
	 * par default les propriétés protected sont toutes editable
	 * @return array
	 */
	public function getEditableTypesProperties() {
		if ($this->_editableTypesProperties)
			return $this->_editableTypesProperties;
		$sNamespace = (new \ReflectionClass($this))->getNamespaceName();
		$aProperties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED);
		foreach($aProperties as $property) {
			$value = null;
			try {
				// on check le type déclaré
				if (preg_match('#@var\s+([^\s]+)#i', $property->getDocComment(), $matches) === 1 && !empty($matches[1])) {
					$detectType = $matches[1];
					if (!in_array($detectType, ['int', 'float', 'string', 'boolean', 'bool', 'array', 'object'])) {
						if ($detectType{0} !== '\\')
							$detectType = $sNamespace . '\\' . $detectType;
						$oClass = new \ReflectionClass($detectType);
						// on assign une valeur uniquement si une class est instantiable
						if ($oClass->isInstantiable()) {
							$value = $detectType;
						}
						unset($oClass);
					}
				}
			} catch (\ReflectionException $e) {  }
			$this->_editableTypesProperties[$property->getName()] = $value;
		}
		return $this->_editableTypesProperties;
	}

	/**
	 * Charge le model a partir d'un tableau de données
	 * @param array $dataset [attribut => 'valeur', ...]
	 * @throws \LogicException
	 * @return void
	 */
	public function populate(array $dataset) {
		if (!empty($dataset)) {
			foreach ($this->getEditableProperties() as $propName) {
				if (isset($dataset[$propName]))
					$this->offsetSet($propName, $dataset[$propName]);
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
		foreach ($this->getEditableProperties() as $propName) {
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
	 *    isset($model->attribut);
	 * @param string $offset
	 * @return bool|void
	 */
	public function __isset($offset) {
		return $this->offsetExists($offset);
	}

	/**
	 * permet l'edition de propriétés via l'objet comme si les attribut était public :
	 *    $model->attribut = 'new value';
	 * @param string $offset
	 * @param mixed $value
	 */
	public function __set($offset, $value) {
		$this->offsetSet($offset, $value);
	}

	/**
	 * permet l'accès aux propriétés via l'objet comme si les attribut était public :
	 *    echo $model->attribut;
	 * @param string $offset
	 * @return mixed|void
	 */
	public function __get($offset) {
		return $this->offsetGet($offset);
	}

	/**
	 * Permet la suppression d'une propriété via l'objet comme si les attribut était public :
	 *    unset($model->attribut);
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
		return in_array($offset, $this->getEditableProperties());
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
		if ($this->offsetExists($offset)) {
			$aEditableTypes = $this->getEditableTypesProperties();
			if (!empty($aEditableTypes[$offset])) {
				$value = new $aEditableTypes[$offset]($value);
			}
			$this->$offset = $value;
		}
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