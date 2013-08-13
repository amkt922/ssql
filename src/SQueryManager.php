<?php

namespace SSql;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SSql
 *
 * @author amkt
 */
class SQueryManager {

	/**
	 * PHP Data objects.
	 * @var type resource
	 */
    private $pdo = null;

	/**
	 * the sql that is going to be executed.
	 * @var type array
	 */
	private $sqlStack = array();

	private $inputParameters = array();
    
    /**
     * constructor
     */
    public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function insert() {
		array_push($this->sqlStack, 'INSERT');
		return $this;
	}

	public function delete() {
		array_push($this->sqlStack, 'DELETE');
		return $this;
	}

	public function select($columns) {
		array_push($this->sqlStack, 'SELECT');
		if (is_array($columns)) {
			array_push($this->sqlStack, implode(',', $columns));
		} else {
			array_push($this->sqlStack, $columns);
		}
		return $this;
	}

	public function from($table) {
		array_push($this->sqlStack, 'FROM');
		array_push($this->sqlStack, $table);
		return $this;
	}

   	public function into($table, $columns = array()) {
		array_push($this->sqlStack, $table);
		array_push($this->sqlStack, '(' . implode(',', $columns) . ')');
		return $this;
	}

   	public function values($values = array()) {
		array_push($this->sqlStack, 'VALUES');
		$this->inputParameters = $values;
		$valuesNum = count($values);
		$valueNum = count($values[0]);
		for ($i = 0; i < $valuesNum; $i++) {
			$v = array_fill(0, $valueNum, '?');
			array_push($this->sqlStack, '(' . implode(',', $v) . ')');
		}
		return $this;
	}

	public function update($table) {
		array_push($this->sqlStack, 'UPDATE');
		array_push($this->sqlStack, $table);
		return $this;
	}

	public function set($values) {
		array_push($this->sqlStack, 'SET');
		$setValues = array();
		foreach ($values as $key => $value) {
			array_push($setValues, "{$key} = ?");
			array_push($this->inputParameters, $value);
		}
		array_push($this->sqlStack, implode(',', $setValues));
		return $this;
	}

	public function where($conditions) {
		array_push($this->sqlStack, 'WHERE');
		$sql = array();
		foreach ($conditions as $column => $value) {
			array_push($sql, "$column = ?");
			array_push($this->inputParameters, $value);
		}
		array_push($this->sqlStack, '(' . implode(' AND ', $sql) . ')');
		return $this;
	}

	public function orClause($conditions) {
		array_push($this->sqlStack, 'OR');
		$this->where($conditions);
		return $this;
	}

	public function andClause($conditions) {
		array_push($this->sqlStack, 'AND');
		$this->where($conditions);
		return $this;
	}


	public function execute() {
		$sql = implode(' ', $this->sqlStack);
		$stmt = $this->pdo->prepare($sql);
		if (mb_strpos($sql, 'SELECT') === 0) {
			$stmt->execute($this->inputParameters);
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} else {
			return $stmt->execute($this->inputParameters);
		}
	}
}

