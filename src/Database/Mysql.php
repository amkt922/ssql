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

use \PDO;
	
/**
 * @author amkt922
 */
class Mysql extends AbstractDriver {

	public function connect() {
		$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

		if (isset($this->config['encoding'])) {
			$options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $this->config['encoding'];
		}

        $this->pdo = new PDO($this->config['dsn']
							, $this->config['user']
							, $this->config['password']
							, $options);
		
	}    
}

