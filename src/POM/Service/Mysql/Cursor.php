<?php
/**
 * @author: Nicolas Levée
 * @version 180320141728
 */

namespace POM\Service\Mysql;

/**
 * Class Cursor
 * @package POM\Service\Mysql
 */
class Cursor implements \Iterator {

	/**
	 * @var \PDOStatement
	 */
	private $_statement;

	/**
	 * @var array
	 */
	private $_row;

	/**
	 * @var int
	 */
	private $_position = 0;

	/**
	 * @var int
	 */
	private $_total;

	/**
	 * @var array
	 */
	private $_callback;


	/**
	 * @param \PDOStatement $statement
	 */
	public function __construct(\PDOStatement $statement) {
		$this->_statement = $statement;
		$this->_total = $statement->rowCount();
		$this->_row = $this->_statement->fetch();
	}

	/**
	 * ferme le curseur
	 */
	public function __destruct() {
		$this->_statement->closeCursor();
	}

	/**
	 * @param callable $callback
	 * @param array $args
	 */
	public function setRowHandler(\Closure $callback, array $args = []) {
		$this->_callback = [$callback, $args];
	}


	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		if ($this->_callback) {
			return call_user_func_array($this->_callback[0], array_merge([$this->_row], $this->_callback[1]));
		}
		return $this->_row;
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		$this->_row = $this->_statement->fetch();
		$this->_position++;
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return $this->_position;
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return $this->_total > $this->_position;
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @throws \OutOfBoundsException
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		if ($this->_position > 0)
			throw new \OutOfBoundsException("No rewind on a mysql statement");
	}
}