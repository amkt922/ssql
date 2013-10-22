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
 * Simple outside Sql Manager.
 *
 * @author amkt922
 */
class SSqlManager {

    /**
     * Hold the database connection
     * @var mixed The derived class of AbstractDriver
     */
    private $con = null;
    
	/**
	 * The place to store sql files.
	 * @var string
	 */
	private $sqlDir = '';
  
    /**
     * constructor
     *
     * @param mixed $con
     * @param string $sqlDir
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

    /**
     * Fetch data from database with passed outside sql.
     * <pre>
     * passed sql is a outside sql file path, SSqlManager parses sql written in it
     * and replaces them with passed parameter.
     * </pre>
     * @param string $sql outside sql file name without extension.
     * @param array $params parameter that passes outside sql.
     * @param string|null $entityName result class when want to store it, otherwise return is an array..
     * @return mixed
     */
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

    /**
     * fetch one result data from database with passed outside sql.
     * The process is same as selectList, for one result.
     * @param string $sql outside sql file name without extension.
     * @param array $params parameter that passes outside sql.
     * @param string|null $entityName result class when want to store it, otherwise return is an array..
     * @return mixed
     */
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

    /**
     * execute sql. A sql is from parameter sql and parse and build with params.
     *
     * @param string $sql outside sql file name without extension.
     * @param array $params parameter that passes outside sql.
     * @return mixed same as PDO::exec
     */
    public function execute($sql, $params) {
		$sql = $this->setupSql($sql, $params);
		$result = $this->con->getConnection()->exec($sql);
        $this->executeLog($sql, $result);
        return $result;
	}

    private function executeLog($sql, $result) {
        $resultNum = count($result);
        $message = <<<MSG
<<<<<<<<<<start 
call SSqlManager::execute 
result num is {$resultNum}
SQL
{$sql}
>>>>>>>>>>end

MSG;
        $logger = SLog::getLogger();
        $logger->info($message);
    }
}

