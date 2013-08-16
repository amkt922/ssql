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

namespace SSql\Sql\Node;

use SSql\Sql\Node\AbstractNode;

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
class SqlConnectorNode extends AbstractNode {
   
	private $connector = null;
	private $sql = null;
	private $independent = false;

	public function __construct($connector, $sql) {
		$this->connector = $connector;
		$this->sql = $sql;
    }

	public static function createSqlConnectorNode($connector, $sql) {
		return new self($connector, $sql);	
	}

	public static function createSqlConnectorNodeAsIndependent($connector, $sql) {
		$obj = new self($connector, $sql);	
		$obj->asIndependent();
		return $obj;
	}

	private function asIndependent() {
		$this->independent = true;
		return $this;
	}

	public function getConnector() {
		return $this->connector;
	}

	public function getSql() {
		return $this->sql;
	}

	public function acceptContext($context) {
		if ($context->isEnabled()) {
			$context->addSql($this->connector);
		}
		$context->addSql($this->sql);		
	}
}

