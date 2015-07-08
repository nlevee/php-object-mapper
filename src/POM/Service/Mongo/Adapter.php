<?php
/**
 * @author: Nicolas Levée
 * @version 240320141048
 */

namespace POM\Service\Mongo;

use POM\Service\AdapterInterface;

/**
 * Class Adapter
 * @package POM\Service\Mongo
 */
class Adapter implements AdapterInterface {

	/**
	 * @var \MongoDB
	 */
	protected $_dbHandler;


	/**
	 * @param string $dsn
	 * @param string $name
	 * @param array $opts
	 * @throws \MongoConnectionException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($dsn, $name, array $opts = array()) {
		try {
			if (!$dsn || !preg_match("@^mongodb://.+@", $dsn))
				throw new \InvalidArgumentException('Mongo DSN invalid, it must match : ^mongodb://.+');
			$oMongoCli = new \MongoClient($dsn, $opts);
			$this->_dbHandler = $oMongoCli->selectDB($name);
		} catch(\MongoConnectionException $e) {
			error_log((string) $e);
			throw $e;
		}
	}

}