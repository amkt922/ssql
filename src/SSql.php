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

require_once "autoload.php";

use SSql\SSqlManager;
use SSql\SQueryManager;

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

	private static $instance = null;
  
    /**
     * constructor
     */
	private function __construct() {}

    public function connect($config) {
		if (is_null(self::$instance)) {
			self::$instance = new self;
			if (is_array($config)) {
				self::$instance->setConfigFromArray($config);
				self::$instance->setupPDO();
			} else {
				throw new \RuntimeException('config should be an array');
			}
		}
		return self::$instance;
	}
    
    /**
     * create instance of myself and load config file.
     * 
     * @param array|string  $config  config file for accessing database.
     */
    public function createSSql() {
        return new SSqlManager($this->pdo, $this->sqlDir);
    }

    public function createSQry() {
        return new SQueryManager($this->pdo);
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

	private function setupPDO() {
        if (is_null($this->pdo)) {
            $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

	public function beginTransaction() {
		return $this->pdo->beginTransaction();
	}

	public function commit() {
		return $this->pdo->commit();
	}

	public function rollback() {
		return $this->pdo->rollback();
	}
}

