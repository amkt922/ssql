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
class Postgres extends AbstractDriver {

	public function connect() {
		$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

        $this->pdo = new PDO($this->config['dsn']
							, $this->config['user']
							, $this->config['password']
							, $options);
		if (isset($this->config['encoding'])) {
			$this->execute('SET NAMES ' . $this->config['encoding']);
		}
		if (isset($this->config['schema'])) {
			$this->execute('SET search_path TO ' . $this->config['schema']);
		}
	}

    public function tables() {
  		$schema = $this->config['schema'];
      	$sql = "SELECT table_name as name FROM INFORMATION_SCHEMA.tables WHERE table_schema = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($schema));

        $tables = array();
        while ($table = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $table[0];
        }
        return $tables;
    }

    public function columnsOf($table) {
        $schema = $this->config['schema'];
        $sql = <<<SQL
SELECt column_name AS name
        , data_type AS type
        , CASE WHEN column_name IN (SELECT c.column_name
				                    FROM information_schema.table_constraints tc
					                      JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_schema = tc.constraint_schema
                                                                                                  AND ccu.constraint_name = tc.constraint_name
					                      JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema
                                                                                AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
				                    WHERE constraint_type = 'PRIMARY KEY' AND tc.table_name = ?)
        THEN '1'
        ELSE '0'
        END AS PK
FROM
  information_schema.columns
WHERE
  table_schema = ?
  AND table_name = ?
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($table, $schema, $table));
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

