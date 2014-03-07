<?php

/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 */
class SplClassLoader {
	/**
	 * @var string
	 */
	private $_fileExtension = '.php';

	/**
	 * @var null|string
	 */
	private $_namespace;

	/**
	 * @var null
	 */
	private $_includePath;

	/**
	 * @var string
	 */
	private $_namespaceSeparator = '\\';


	/**
	 * Register new autloader on $ns
	 * @param string $ns
	 * @param string|null $includePath
	 * @param string|null $fileExt
	 */
	public static function AutoloadRegister($ns, $includePath = null, $fileExt = null) {
		$me = new self($ns, $includePath);
		if (null !== $fileExt)
			$me->setFileExtension($fileExt);
		$me->register();
	}

	/**
	 * unregister the autload $ns
	 * @param string $ns
	 */
	public static function AutloadUnregister($ns) {
		foreach (spl_autoload_functions() as $func) {
			if (isset($func[0]) && $func[0] instanceof SplClassLoader && $func[0]->getNamespace() == $ns)
				spl_autoload_unregister($func);
		}
	}


	/**
	 * Creates a new <tt>SplClassLoader</tt> that loads classes of the
	 * specified namespace.
	 * @param string $ns The namespace to use.
	 * @param null $includePath
	 */
	public function __construct($ns = null, $includePath = null) {
		$this->_namespace = $ns;
		$this->_includePath = $includePath;
	}

	/**
	 * Get the namespace
	 * @return null|string
	 */
	public function getNamespace() {
		return $this->_namespace;
	}

	/**
	 * Sets the namespace separator used by classes in the namespace of this class loader.
	 * @param string $sep The separator to use.
	 */
	public function setNamespaceSeparator($sep) {
		$this->_namespaceSeparator = $sep;
	}

	/**
	 * Gets the namespace seperator used by classes in the namespace of this class loader.
	 * @return string
	 */
	public function getNamespaceSeparator() {
		return $this->_namespaceSeparator;
	}

	/**
	 * Sets the base include path for all class files in the namespace of this class loader.
	 * @param string $includePath
	 */
	public function setIncludePath($includePath) {
		$this->_includePath = $includePath;
	}

	/**
	 * Gets the base include path for all class files in the namespace of this class loader.
	 * @return string $includePath
	 */
	public function getIncludePath() {
		return $this->_includePath;
	}

	/**
	 * Sets the file extension of class files in the namespace of this class loader.
	 * @param string $fileExtension
	 */
	public function setFileExtension($fileExtension) {
		$this->_fileExtension = $fileExtension;
	}

	/**
	 * Gets the file extension of class files in the namespace of this class loader.
	 * @return string $fileExtension
	 */
	public function getFileExtension() {
		return $this->_fileExtension;
	}

	/**
	 * Installs this class loader on the SPL autoload stack.
	 */
	public function register() {
		spl_autoload_register(array($this, 'loadClass'));
	}

	/**
	 * Uninstalls this class loader from the SPL autoloader stack.
	 */
	public function unregister() {
		spl_autoload_unregister(array($this, 'loadClass'));
	}

	/**
	 * Loads the given class or interface.
	 * @param string $className The name of the class to load.
	 * @return bool
	 */
	public function loadClass($className) {
		$success = false;
		if (null === $this->_namespace || $this->_namespace . $this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace . $this->_namespaceSeparator))) {
			$fileName = '';
			if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
				$namespace = substr($className, 0, $lastNsPos);
				$className = substr($className, $lastNsPos + 1);
				$fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
			}
			$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
			if ($this->_includePath !== null)
				$fileName = $this->_includePath . DIRECTORY_SEPARATOR . $fileName;
			if (($fileName = stream_resolve_include_path($fileName)) !== false && is_readable($fileName)) {
				require $fileName;
				$success = true;
			}
		}
		return $success;
	}
}