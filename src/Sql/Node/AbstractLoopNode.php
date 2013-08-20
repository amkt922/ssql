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

use SSql\Sql\Node\ScopeNode;
use SSql\Sql\Node\LoopAcceptable;

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
abstract class AbstractLoopNode extends ScopeNode implements LoopAcceptable {

	private $expression = null;

	private $sql = null;

	public function __construct($expession, $sql) {
		$this->expression = $expession;
		$this->sql = $sql;
	}

	public function acceptContext($context) {
		//TODO imple throw exception	
	}

	public function acceptLoopInfo($context, $loopInfo) {
		if (!$this->isValid($loopInfo->getLoopSize(), $loopInfo->getLoopIndex())) {
			return;
		}
		$this->processAcceptingChilden($context, $loopInfo);	
	}

	abstract function isValid($loopSize, $loopIndex);

}
