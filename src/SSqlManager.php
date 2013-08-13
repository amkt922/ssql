<?php

namespace SSql;

use SSql\Sql\Context\CommandContext;
use SSql\Sql\SqlAnalyzer;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SSql
 *
 * @author amkt
 */
class SSqlManager {

	/**
	 * PHP Data objects.
	 * @var type resource
	 */
    private $pdo = null;
    
	/**
	 * The place to store sql files.
	 * @var type string
	 */
	private $sqlDir = '';
  
    /**
     * constructor
     */
    public function __construct(\PDO $pdo, $sqlDir) {
		$this->pdo = $pdo;
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
		$stmt = $this->pdo->prepare($context->getSql());
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
		return $this->pdo->exec($sql);
	}
}

