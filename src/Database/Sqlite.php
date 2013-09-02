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
class Sqlite extends AbstractDriver {

	public function connect() {
		$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

        $this->pdo = new PDO($this->config['dsn']
							, null
							, null
							, $options);
		
	}


    public function tables() {
        $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type='table'");
        $stmt->execute();
        $tables = array();
        while ($table = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tables[] = $table['name'];
        }
        return $tables;
    }

    public function columnsOf($table) {
        $stmt = $this->pdo->prepare("PRAGMA table_info({$table})");
        $stmt->execute();
        $columns = array();
        while ($column = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $c = array('name' => $column['name']
                        , 'type' => $column['type']
                        , 'pk' => false);
            if ($column['pk'] === '1') {
                $c['pk'] = true;
            }
            $columns[] = $c;
        }
        return $columns;
    }
}

