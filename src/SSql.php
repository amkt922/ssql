<?php

namespace SSql;

require_once "autoload.php";


use SSql\Sql\Context\CommandContext;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SSql
 *
 * @author amkt
 */
class SSql {
    private $pdo = null;
    
    private $dsn = '';
    
    private $user = '';
    
    private $password = '';    

	private $sqlDir = '';

    /**
     * @var SSql instance of myself 
     */
    private static $instance = null;
   
    /**
     * constructor
     */
    private function __construct() {}
    
    /**
     * create instance of myself and load config file.
     * 
     * @param array|string  $config  config file for accessing database.
     */
    public static function getSSql($config) {
        if (is_null(self::$instance)) {
            self::$instance = new self;
			if (is_array($config)) {
				self::$instance->setConfigFromArray($config);
			}   
        }
        
        return self::$instance;
    }

    private function setConfigFromArray($config) {        
        if (!in_array('database', $config) 
                && !is_array($config['database'])) {
            throw new \InvalidArgumentException('The parameter sould include database and it should be an array.');
        }
        if (!array_key_exists('dsn', $config['database'])) {
            throw new \InvalidArgumentException('dsn value should be in database array.');
        }
        $database = $config['database'];
        $this->dsn = $database['dsn'];
        if (array_key_exists('user', $database)) {
            $this->user = $database['user'];
        }
        if (array_key_exists('password', $database)) {
            $this->password = $database['password'];
        }

		if (array_key_exists('sqlDir', $config)) {
			$this->sqlDir = $config['sqlDir'];
		}
    }

	private function getCommandContext($sql, $params) {
		$this->setupPDO();
		$rowSql = file_get_contents($this->sqlDir . $sql . '.sql');
		$analyzer = new \SSql\Sql\SqlAnalyzer($rowSql);
		$node = $analyzer->analyze();
		$context = CommandContext::createCommandContext($params);
		$node->acceptContext($context);
		return $context;
	}

	private function setupPDO() {
        if (is_null($this->pdo)) {
            $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
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

