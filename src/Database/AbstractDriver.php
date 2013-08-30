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

namespace SSql\Database;

use PDO;

/**
 * @author amkt922
 */
abstract class AbstractDriver {
    
	protected $pdo = null;

	protected $config = array();

	public function __construct($config) {
		$this->config = $config;
	}

	public function getConnection () {
		return $this->pdo;
	}

    public function close() {
        $this->pdo = null;
    }

    protected function execute($sql, $params = array(), $prepareOptions = array()) {
        $stmt = $this->pdo->prepare($sql, $prepareOptions);
        $stmt->setFetchMode(PDO::FETCH_LAZY);
        return $stmt->execute($params);
    }

	abstract public function connect();

    abstract public function tables();

    abstract public function columnsOf($table);

}

