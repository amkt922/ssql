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

namespace SSql\Sql\Node;

use SSql\Sql\Node\AbstractNode;
use SSql\Sql\Node\SqlConnectorAdjustable;
use SSql\Sql\Context\CommandContext;

/**
 * @author reimplement in PHP and modified by amkt (originated in dbflute) 
 */
class BeginNode extends ScopeNode implements SqlConnectorAdjustable {
    
    const MARK = 'BEGIN';

	private $nested = false;

    public function __construct($nested = false) {
		$this->nested = $nested;
    }

	public function isNested() {
		return $this->nested;
	}

	public function acceptContext($context) {
		$childContext = CommandContext::createCommandContextAsBeginChild($context); 
		$this->processAcceptingChilden($childContext);
		if ($childContext->isEnabled()) {
			$context->addSql($childContext->getSql());	
			$context->addBindVariables($childContext->getBindVariables());	
			$context->addBindVariableTypes($childContext->getBindVariableTypes());	
		}		
	}
	
}

