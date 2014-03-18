<?php
/**
 * @author: Nicolas Levée
 * @version 070320141502
 */

namespace POM\Service\Mysql;

use POM\IdentityMap;
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
	 * @var \PDO
	 */
	private $_dbHandler;


	/**
	 * @param string $dsn
	 * @param string $user
	 * @param string $pass
	 * @param array $opts
	 * @throws \InvalidArgumentException
	 */
	public function __construct($dsn, $user, $pass, array $opts = array()) {
		if (!$dsn || !preg_match("@^mysql:host=[^;]+;dbname=.+(;port=.+)?$@", $dsn))
			throw new \InvalidArgumentException('Mysql DSN invalid, it must match : ^mysql:host=.+;dbname=.+$');
		$this->_dbAccess['dsn'] = $dsn;
		$this->_dbAccess['user'] = $user;
		$this->_dbAccess['pass'] = $pass;
		$this->_dbAccess['opts'] = array_merge(array(
				\PDO::MYSQL_ATTR_FOUND_ROWS => true,
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
		), $opts);
		// on utilise une identity map pour stocké les statements afin de les réutiliser
		$this->_stmtMap = new IdentityMap();
	}

	/**
	 * detruit la connection actuellement ouverte
	 */
	public function __destruct() {
		unset($this->_dbHandler);
	}

	/**
	 *
	 */
	public function connect() {
		$this->_dbHandler = new \PDO($this->_dbAccess['dsn'], $this->_dbAccess['user'], $this->_dbAccess['pass'], $this->_dbAccess['opts']);
	}

	/**
	 * Renvoi une ligne seulement de la requete demandé sous forme de tableau
	 * @param string $query
	 * @param array $bind
	 * @return array
	 */
	public function fetchOne($query, array $bind = array()) {
		return $this->fetch($query, $bind)->current();
	}

	/**
	 * Renvoi un curseur pour parcourir chaque ligne de la requete,
	 * il faudra penser a détruire le curseur afin de libéré la resource
	 * @param string $query
	 * @param array $bind
	 * @return Cursor
	 */
	public function fetch($query, array $bind = array()) {
		// execution de la nouvelle requete
		$stmt = $this->getStatementForQuery($query);
		$stmt->execute($bind);
		$stmt->setFetchMode(\PDO::FETCH_ASSOC);
		return new Cursor($stmt);
	}

	/**
	 * Renvoi les resultats d'une colonne uniquement, soit son numero soit son nom
	 * @param int|string $column
	 * @param string $query
	 * @param array $bind
	 * @return Cursor
	 */
	public function fetchColumn($column, $query, array $bind = array()) {
		$stmt = $this->getStatementForQuery($query);
		$stmt->execute($bind);
		$stmt->setFetchMode(\PDO::FETCH_COLUMN, $column);
		return new Cursor($stmt);
	}
	
	/**
	 * Execute une liste de requete dans une transaction et renvoi le nombre total de ligne affecté
	 * si $lastInsertId est fourni il est rempli avec le dernier ID inséré
	 * @param array $queryList [ ['SQL QUERY', ['PARAM'=>'VALUE', ...]], 'SQL QUERY', ... ]
	 * @param int $lastInsertId
	 * @return int
	 */
	public function execInTransaction(array $queryList, &$lastInsertId = null) {
		try {
			$rowCount = 0;
			$this->_dbHandler->beginTransaction();
			foreach ($queryList as $queryParam) {
				if (is_string($queryParam))
					$rowCount += $this->exec($queryParam, []);
				elseif(is_array($queryParam)) 
					$rowCount += $this->exec($queryParam[0], $queryParam[1]);
			}
			$this->_dbHandler->commit();
			$lastInsertId = $this->_dbHandler->lastInsertId();
		} catch(\PDOExecption $e) {
			$this->_dbHandler->rollback();
			throw $e;
		}
		return $rowCount;
	}

	/**
	 * Effectue une requete et renvoi le nobre de ligne affecté, 
	 * si $lastInsertId est fourni il est rempli avec le dernier ID inséré
	 * @param string $query
	 * @param array $bind
	 * @param int $lastInsertId
	 * @return int
	 */
	public function exec($query, array $bind = array(), &$lastInsertId = null) {
		$stmt = $this->getStatementForQuery($query);
		$stmt->execute($bind);
		$lastInsertId = $this->_dbHandler->lastInsertId();
		return $stmt->rowCount();
	}
	
	/**
	 * Renvoi l'instance de DBHandler
	 * @return \PDO
	 */
	public function getDbHandler() {
		return $this->_dbHandler;
	}


	/**
	 * Renvoi le statement associé a une query
	 * @param string $query
	 * @return \PDOStatement
	 */
	protected function getStatementForQuery($query) {
		// on verifie que le statement n'est pas deja présent
		if (!$this->_stmtMap->hasId($query)) {
			$stmt = $this->_dbHandler->prepare($query);
			$this->_stmtMap->storeObject($query, $stmt);
		} else
			$stmt = $this->_stmtMap->getObject($query);
		return $stmt;
	}

}