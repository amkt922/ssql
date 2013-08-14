<?php
/*
  Copyright 2013, amkt <amkt922@gmail.com>

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

	public function distinct() {
		array_push($this->sqlStack, 'DISTINCT');
		return $this;
	}

	public function innerJoin($table, $conditions) {
		array_push($this->sqlStack, 'INNER JOIN');
		array_push($this->sqlStack, $table);
		array_push($this->sqlStack, 'ON');
		$this->where($conditions, false);
		return $this;
	}

	public function leftJoin($table, $conditions) {
		array_push($this->sqlStack, 'LEFT OUTER JOIN');
		array_push($this->sqlStack, $table);
		array_push($this->sqlStack, 'ON');
		$this->where($conditions, false);
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

	public function where($conditions, $whereClause = true) {
		if ($whereClause) {
			array_push($this->sqlStack, 'WHERE');
		}
		$sql = array();
		foreach ($conditions as $column => $value) {
			array_push($sql, "$column = ?");
			array_push($this->inputParameters, $value);
		}
		array_push($this->sqlStack, '(' . implode(' AND ', $sql) . ')');
		return $this;
	}

	public function orWhere($conditions) {
		array_push($this->sqlStack, 'OR');
		$this->where($conditions, false);
		return $this;
	}

	public function andWhere($conditions) {
		array_push($this->sqlStack, 'AND');
		$this->where($conditions, false);
		return $this;
	}

	public function having($conditions, $havingClause = true) {
		if ($havingClause) {
			array_push($this->sqlStack, 'HAVING');
		}
		$sql = array();
		foreach ($conditions as $column => $value) {
			array_push($sql, "$column = ?");
			array_push($this->inputParameters, $value);
		}
		array_push($this->sqlStack, '(' . implode(' AND ', $sql) . ')');
		return $this;
	}

	public function orHaving($conditions) {
		array_push($this->sqlStack, 'OR');
		$this->having($conditions, false);
		return $this;
	}

	public function andHaving($conditions) {
		array_push($this->sqlStack, 'AND');
		$this->having($conditions, false);
		return $this;
	}

	public function orderBy($clauses) {
		array_push($this->sqlStack, 'ORDER BY');
		$orders = array();
		foreach ($clauses as $clause => $order) {
			array_push($orders, "{$clause} {$order}");
		}
		array_push($this->sqlStack, implode(',', $orders));
		return $this;
	}

	public function groupBy($clauses) {
		array_push($this->sqlStack, 'GROUP BY');
		array_push($this->sqlStack, implode(',', $clauses));
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

