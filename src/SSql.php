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

require_once "autoload.php";

use \InvalidArgumentException;
use SSql\SSqlManager;
use SSql\SQueryManager;
use SSql\Database\DriverManager;

/**
 * SSql factory class.
 *
 * @author amkt922
 */
class SSql {

    private $con = null;
    
	private $sqlDir = '';

	private static $instance = null;
  
    /**
     * constructor
     */
	private function __construct() {}

    public static function connect($config) {
		if (is_null(self::$instance)) {
			self::$instance = new self;
			if (is_array($config)) {
				self::$instance->setConfigFromArray($config);
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
        return new SSqlManager($this->con, $this->sqlDir);
    }

    public function createSQry() {
        return new SQueryManager($this->con);
    }


    private function setConfigFromArray($config) {        
        if (!in_array('database', $config) 
                && !is_array($config['database'])) {
            throw new InvalidArgumentException('The parameter sould include database and it should be an array.');
        }
        if (!array_key_exists('dsn', $config['database'])) {
            throw new InvalidArgumentException('dsn value should be in database array.');
        }
        $database = $config['database'];
		$this->con = DriverManager::getConnection($database);

		if (array_key_exists('sqlDir', $config)) {
			$this->sqlDir = $config['sqlDir'];
		}
    }

	public function beginTransaction() {
		return $this->con->getConnection()->beginTransaction();
	}

	public function commit() {
		return $this->con->getConnection()->commit();
	}

	public function rollback() {
		return $this->con->getConnection()->rollback();
	}
}

