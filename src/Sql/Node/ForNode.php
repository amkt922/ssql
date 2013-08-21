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

use SSql\Sql\Node\SqlConnectorAdjustable;
use SSql\Sql\Node\IfCommentEvaluator;
use SSql\Sql\Node\ParameterFinder;

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
class ForNode extends ScopeNode implements SqlConnectorAdjustable, LoopAcceptable {
    
    const PREFIX = 'For ';

	const CURRENT_VARIABLE = '#current';
    
    private $expression = null;
    
    private $sql = null;

    
    public function __construct($expression, $sql) {
		$this->expression = $expression;
		$this->sql = $sql;
    }

	public function acceptContext($context) {
		$this->doAccept($context, null);
	}

	public function acceptLoopInfo($context, $loopInfo) {
		if ($this->expression === self::CURRENT_VARIABLE) {
			$parameter = $loopInfo->getCurrentParmeter();
			$this->doAcceptWithParameter($context, $parameter, $loopInfo);
		} else {
			$this->doAccept($context, $loopInfo);
		}
	}

	private function doAccept($context, $loopInfo) {
		$parameter = $context->getArg($this->expression);
		$this->doAcceptWithParameter($context, $parameter, $loopInfo);
	}

	private function doAcceptWithParameter($context, $parameter, $parentLoop) {
		if (is_null($parameter)) {
			return;
		}
		$loopInfo = new LoopInfo();
		$loopInfo->setParentLoop($parentLoop);
		$loopInfo->setExpression($this->expression);
		$loopInfo->setSql($this->sql);
		$loopInfo->setParameterList($parameter);
		$loopSize = count($parameter);
		$loopInfo->setLoopSize($loopSize);
		foreach ($parameter as $index => $value) {
			$loopInfo->setLoopIndex($index);
			$this->processAcceptingChilden($context, $loopInfo);
		}
		if ($loopSize > 0) {
			$context->setEnabled(true);
		}
	}

}

