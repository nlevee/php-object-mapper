<?php
/**
 * @author: Nicolas Levée
 * @version 070320141502
 */

namespace POM\Service\Mysql;

use POM\Service\AdapterInterface;

/**
 * Class Adapter
 * @package POM\Service\Mysql
 */
class Adapter implements AdapterInterface {

	/**
	 * @var array
	 */
	private $_dbAccess = array();

	/**
	 * @param string $dsn
	 * @param string $user
	 * @param string $pass
	 * @param array $opts
	 * @throws \InvalidArgumentException
	 */
	public function __construct($dsn, $user, $pass, array $opts = array()) {
		if (!$dsn || !preg_match("@^mysql:host=[^;]+;dbname=.+$@", $dsn))
			throw new \InvalidArgumentException('Mysql DSN invalid, it must match : ^mysql:host=.+;dbname=.+$');
		$this->_dbAccess['dsn'] = $dsn;
		$this->_dbAccess['user'] = $user;
		$this->_dbAccess['pass'] = $pass;
		$this->_dbAccess['opts'] = array_merge(array(
				\PDO::MYSQL_ATTR_FOUND_ROWS => true,
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
		), $opts);
	}

	/**
	 * Renvoi une ligne seulement de la requete demandé sous forme de tableau
	 * @param string $query
	 * @param array $bind
	 * @return array
	 */
	public function fetchOne($query, array $bind = array()) {

	}


	public function fetch($query, $bind) {

	}

	public function exec() {

	}


}