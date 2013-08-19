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

use SSql\Sql\Context\CommandContext;
use SSql\Sql\SqlAnalyzer;

/**
 * Simple Outside Sql Manager.
 *
 * @author amkt922
 */
class SSqlManager {

    private $con = null;
    
	/**
	 * The place to store sql files.
	 * @var string
	 */
	private $sqlDir = '';
  
    /**
     * constructor
     */
    public function __construct($con, $sqlDir) {
		$this->con = $con;
		$this->sqlDir = $sqlDir;
	}
    
	private function getCommandContext($sql, $params) {
		$rowSql = file_get_contents($this->sqlDir . $sql . '.sql');
		$analyzer = new SqlAnalyzer($rowSql);
		$node = $analyzer->analyze();
		$context = CommandContext::createCommandContext($params);
		$node->acceptContext($context);
		return $context;
	}

	private function prepareAndBindVariable($context) {
		$stmt = $this->con->getConnection()->prepare($context->getSql());
		$bindVariables = $context->getBindVariables();
		foreach ($bindVariables as $index => $value) {
			$stmt->bindValue($index + 1, $value);
		}
		return $stmt;
	}

	public function selectList($sql, $params, $entityName = null) {
		$context = $this->getCommandContext($sql, $params);
		$stmt = $this->prepareAndBindVariable($context);
		$stmt->execute();
		if (!is_null($entityName)) {
			return $stmt->fetchAll(\PDO::FETCH_CLASS, $entityName);
		} else {
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
	}

	public function selectEntity($sql, $params, $entityName = null) {
		$context = $this->getCommandContext($sql, $params);
		$stmt = $this->prepareAndBindVariable($context);
		$stmt->execute();
		if (!is_null($entityName)) {
			$result = $stmt->fetchAll(\PDO::FETCH_CLASS, $entityName);
		} else {
			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		if(!empty($result)) {
			return $result[0];
		}
		return null;
	}

	public function execute($sql, $params) {
		$sql = $this->setupSql($sql, $params);
		return $this->con->getConnection()->exec($sql);
	}
}

