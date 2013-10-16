<?php
/*
  Copyright 2013, amkt922 <amkt922@gmail.com>

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 */

namespace SSql;

use \PDO;
use \InvalidArgumentException;

/**
 * Simple Query Manager
 * <pre>
 * The class that build sql simply.
 * </pre>
 * 
 * @author amkt922
 */
class SQueryManager {

    /**
     * Hold the database connection
     * @var mixed The derived class of AbstractDriver
     */
    private $con = null;

	/**
	 * The stack that holds sql statements.
	 * @var array
	 */
	private $sqlStack = array();

	/**
	 * parameter that is passed to prepared statement.
	 * @var array
	 */
	private $inputParameters = array();
    
    /**
     * constructor
     * @param mixed $con
     */
    public function __construct($con = null) {
		$this->con = $con;
	}

    /**
     * add INSERT statement
     * @return $this
     */
    public function insert() {
		array_push($this->sqlStack, 'INSERT');
		return $this;
	}

    /**
     * add DELETE statement
     * @return $this
     */
    public function delete() {
		array_push($this->sqlStack, 'DELETE');
		return $this;
	}

	/**
	 * add SELECT statement with columns. If $columns is empty, add '*' for column.
	 * @param array|string $columns selected columns
	 * @return $this
	 */
	public function select($columns) {
		array_push($this->sqlStack, 'SELECT');
		$this->addColumns($columns);
		return $this;
	}

	/**
	 * add SELECT DISTINCT statement with columns. If $columns is empty, add '*' for column.
	 * @param array|string $columns selected columns
	 * @return $this
	 */
	public function selectDistinct($columns) {
		array_push($this->sqlStack, 'SELECT DISTINCT');
		$this->addColumns($columns);
		return $this;
	}

	private function addColumns($columns) {
		if (empty($columns)) {
			$columns = '*';
		}
		if (is_array($columns)) {
			array_push($this->sqlStack, implode(',', $columns));
		} else {
			array_push($this->sqlStack, $columns);
		}
	}

	/**
	 * add INNER JOIN statement with table name and condition for join.
	 * @param string $table
	 * @param array $conditions
	 * @return $this
	 */
	public function innerJoin($table, $conditions) {
		$this->checkJoinFuncParams($table, $conditions);
		array_push($this->sqlStack, 'INNER JOIN');
		$this->addJoinTableNameAndConditions($table, $conditions);
		return $this;
	}

	/**
	 * add LEFT OUTER JOIN statement with table name and condition for join.
	 * @param string $table
	 * @param array $conditions
	 * @return $this
	 */	
	public function leftJoin($table, $conditions) {
		$this->checkJoinFuncParams($table, $conditions);
		array_push($this->sqlStack, 'LEFT OUTER JOIN');
		$this->addJoinTableNameAndConditions($table, $conditions);
		return $this;
	}

	private function checkJoinFuncParams($table, $conditions) {
		if (empty($table)) {
			throw new InvalidArgumentException('1st parameter table should not be emtpy');
		}
		if (empty($conditions)) {
			throw new InvalidArgumentException('2nd parameter condition should not be emtpy');
		}
	}

	private function addJoinTableNameAndConditions($table, $conditions) {
		array_push($this->sqlStack, $table);
		array_push($this->sqlStack, 'ON');
		$sql = array();
		foreach ($conditions as $column => $value) {
			array_push($sql, "{$column} {$value}");
		}
		array_push($this->sqlStack, implode(' AND ', $sql));
	}

    /**
     * add FROM statement with passed table name.
     * @param $table
     * @return $this
     */
    public function from($table) {
		$this->checkTableNameParam($table);
		array_push($this->sqlStack, 'FROM');
		array_push($this->sqlStack, $table);
		return $this;
	}

    /**
     * add INTO statement with passed table name and column names. If columns is empty, not column names set.
     * @param $table
     * @param array $columns
     * @return $this
     */
    public function into($table, $columns = array()) {
		$this->checkTableNameParam($table);
		array_push($this->sqlStack, 'INTO');
		array_push($this->sqlStack, $table);
		if (!empty($columns)) {
			array_push($this->sqlStack, '(' . implode(',', $columns) . ')');
		}
		return $this;
	}

	private function checkTableNameParam($table) {
		if (empty($table)) {
			throw new InvalidArgumentException('parameter table name should not be empty.');
		}
	}

    /**
     * add VALUES statement with passed values. Values are stored in inputParameteters.
     * @param array $values
     * @return $this
     */
    public function values($values) {
		$this->checkValuesParam($values);
		array_push($this->sqlStack, 'VALUES');
		foreach ($values as $v) {
			$this->inputParameters += $v;
		}
		$valuesNum = count($values);
		$valueNum = count($values[0]);
		for ($i = 0; $i < $valuesNum; $i++) {
			$v = array_fill(0, $valueNum, '?');
			array_push($this->sqlStack, '(' . implode(',', $v) . ')');
		}
		return $this;
	}

    /**
     * add UPDATE statement with passed table name.
     * @param string $table
     * @return $this
     */
    public function update($table) {
		$this->checkTableNameParam($table);
		array_push($this->sqlStack, 'UPDATE');
		array_push($this->sqlStack, $table);
		return $this;
	}

    /**
     * add SET statement with passed values.
     * @param array $values
     * @return $this
     */
    public function set($values) {
		$this->checkValuesParam($values);
		array_push($this->sqlStack, 'SET');
		$setValues = array();
		foreach ($values as $key => $value) {
			array_push($setValues, "{$key} = ?");
			array_push($this->inputParameters, $value);
		}
		array_push($this->sqlStack, implode(',', $setValues));
		return $this;
	}

	private function checkValuesParam($values) {
		if (empty($values)) {
			throw new InvalidArgumentException('parameter values should not be empty.');
		}
	}

    /**
     * add WHERE statement with passed conditions.
     * @param array $conditions
     * @param bool $whereClause true when set WHERE statement, false when no WHERE.
     * @return $this
     */
    public function where($conditions, $whereClause = true) {
		$this->checkConditionsParam($conditions);
		if ($whereClause) {
			array_push($this->sqlStack, 'WHERE');
		}
		$sql = array();
		foreach ($conditions as $column => $value) {
			if (is_array($value)
					&& (mb_strpos($column, ' IN') !== false)) {
				$v = array_fill(0, count($value), '?');
				array_push($sql, "$column (" . implode(',', $v) . ')');
				$this->inputParameters += $value;
			} else {
				array_push($sql, "$column ?");
				array_push($this->inputParameters, $value);
			}
		}
		array_push($this->sqlStack, '(' . implode(' AND ', $sql) . ')');
		return $this;
	}

    /**
     * add OR statement in WHERE with passed conditions.
     * @param array $conditions
     * @return $this
     */
    public function orWhere($conditions) {
		$this->checkConditionsParam($conditions);
		array_push($this->sqlStack, 'OR');
		$this->where($conditions, false);
		return $this;
	}

    /**
     * add AND statement in WHERE with passed conditions.
     * @param array $conditions
     * @return $this
     */
    public function andWhere($conditions) {
		$this->checkConditionsParam($conditions);
		array_push($this->sqlStack, 'AND');
		$this->where($conditions, false);
		return $this;
	}

	private function checkConditionsParam($conditions) {
		if (empty($conditions)) {
			throw new InvalidArgumentException('parameter conditions should not empty.');
				}
	}

    /**
     * add HAVING statement with passed conditions.
     * @param array $conditions
     * @param bool $havingClause
     * @return $this
     */
    public function having($conditions, $havingClause = true) {
		$this->checkConditionsParam($conditions);
		if ($havingClause) {
			array_push($this->sqlStack, 'HAVING');
		}
		$sql = array();
		foreach ($conditions as $column => $value) {
			array_push($sql, "$column ?");
			array_push($this->inputParameters, $value);
		}
		array_push($this->sqlStack, '(' . implode(' AND ', $sql) . ')');
		return $this;
	}

    /**
     * add OR statement in HAVING with passed conditions.
     * @param array $conditions
     * @return $this
     */
    public function orHaving($conditions) {
		$this->checkConditionsParam($conditions);
		array_push($this->sqlStack, 'OR');
		$this->having($conditions, false);
		return $this;
	}

    /**
     * add AND statement in HAVING with passed conditions.
     * @param array $conditions
     * @return $this
     */
	public function andHaving($conditions) {
		$this->checkConditionsParam($conditions);
		array_push($this->sqlStack, 'AND');
		$this->having($conditions, false);
		return $this;
	}

    /**
     * add ORDER BY statement with passed clauses.
     * @param array $clauses
     * @return $this
     */
    public function orderBy($clauses) {
		$this->checkClausesParam($clauses);
		array_push($this->sqlStack, 'ORDER BY');
		$orders = array();
		foreach ($clauses as $clause => $order) {
			array_push($orders, "{$clause} {$order}");
		}
		array_push($this->sqlStack, implode(',', $orders));
		return $this;
	}

    /**
     * add GROUP BY statement with passed clauses.
     * @param array $clauses
     * @return $this
     */
    public function groupBy($clauses) {
		$this->checkClausesParam($clauses);
		array_push($this->sqlStack, 'GROUP BY');
		array_push($this->sqlStack, implode(',', $clauses));
		return $this;
	}

	private function checkClausesParam($clauses) {
		if (empty($clauses)) {
			throw new InvalidArgumentException('parameter clauses should not be empty.');

		}
	}

    /**
     * add LIMIT statement with passed number.
     * @param int $num
     * @return $this
     */
    public function limit($num) {
		$this->checkNumParam($num);
		array_push($this->sqlStack, 'LIMIT');
		array_push($this->sqlStack, $num);
		return $this;
	}

    /**
     * add OFFSET statement with passed number.
     * @param int $num
     * @return $this
     */
    public function offset($num) {
		$this->checkNumParam($num);
		array_push($this->sqlStack, 'OFFSET');
		array_push($this->sqlStack, $num);
		return $this;
	}

	private function checkNumParam($num) {
		if (empty($num)) {
			throw new InvalidArgumentException('parameter num should not be empty.');
		}
	}

    /**
     * get the sql built in previous process.
     * @return string
     */
    public function getSql() {
		return implode(' ', $this->sqlStack);
	}

    /**
     * execute the sql built in previous process.
     * @param string $entityName Result set class, an array is returned when entityName is null.
     * @return mixed
     */
    public function execute($entityName = null) {
		$sql = $this->getSql();
		$stmt = $this->con->getConnection()->prepare($sql);
		if (mb_strpos($sql, 'SELECT') === 0) {
			$stmt->execute($this->inputParameters);
			if (!is_null($entityName)) {
				return $stmt->fetchAll(\PDO::FETCH_CLASS, $entityName);
			} else {
				return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			}
		} else {
			return $stmt->execute($this->inputParameters);
		}
	}

    /**
     * get table list in the database.
     * @return array
     */
    public function tables() {
        return $this->con->tables();
    }

     /**
     * get column list in the passed table name.
     * @return array
     */
    public function columnsOf($table) {
        return $this->con->columnsOf($table);
    }
}

