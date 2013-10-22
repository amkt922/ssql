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
use \RuntimeException;
use SSql\SSqlManager;
use SSql\SQueryManager;
use SSql\Database\DriverManager;
use SSql\Log\SLog;

/**
 * SSql factory class.
 *
 * @author amkt922
 * @package SSql
 */
class SSql {

    /**
     * Hold the database connection
     * @var mixed The derived class of AbstractDriver
     */
    private $con = null;

    /**
     * The place where sql files are stored
     * @var string
     */
    private $sqlDir = '';

    /**
     * Instance of SSql itself
     * @var SSql
     */
    private static $instance = null;
  
    /**
     * constructor
     */
	private function __construct() {}

    /**
     * Create SSql instance and connect database with passed config.
     *
     * @param $config
     * @return SSql
     * @throws \RuntimeException when the parameter, config is not array type
     */
    public static function connect($config) {
		if (is_null(self::$instance)) {
			self::$instance = new self;
			if (is_array($config)) {
				self::$instance->setConfigFromArray($config);
			} else {
				throw new RuntimeException('config should be an array');
			}
		}
		return self::$instance;
	}

    /**
     * Close database connection and destroy SSql instance.
     */
    public function close() {
        $this->con->close();
        self::$instance = null;
    }
    
    /**
     * Create SSqlManager instance.
     * @return SSqlManager
     */
    public function createSSql() {
        return new SSqlManager($this->con, $this->sqlDir);
    }

    /**
     * Create SQueryManager instance.
     * @return SQueryManager
     */
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

        // set directory config for sql files.
		if (array_key_exists('sqlDir', $config)) {
			$this->sqlDir = $config['sqlDir'];
		}

        // set logger
        $logConfig = array();
        if (array_key_exists('log', $config)) {
            $logConfig = $config['log'];
        }
        SLog::createLogger($logConfig);
    }

    /**
     * Start transaction with current connection.
     * @return bool true when success otherwise false.
     */
    public function beginTransaction() {
		return $this->con->getConnection()->beginTransaction();
	}

    /**
     * Commit the transaction.
     * @return bool true when success otherwise false
     */
    public function commit() {
		return $this->con->getConnection()->commit();
	}

    /**
     * Rollback the transaction.
     * @return bool true when success otherwise false
     */
    public function rollback() {
		return $this->con->getConnection()->rollback();
	}
}

