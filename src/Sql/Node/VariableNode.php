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
use SSql\Sql\Node\LoopAcceptable;
/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
abstract class VariableNode extends AbstractNode implements LoopAcceptable {

	const PREFIX_NORMAL = '@';

	const PREFIX_REPLACE_ONLY = '@@';

	const PREFIX_TERMINAL_DOT = '@.';
    
	protected  $expression = null;
    protected  $testValue = null;
    
    public function __construct($expression, $testValue) {
		$this->expression = $expression;
		$this->testValue = $testValue;
    }

	public function acceptContext($context) {
		$this->doAccept($context);
	}

    public function acceptLoopInfo($context, $loopInfo) {
		if ($this->expression === ForNode::CURRENT_VARIABLE) {
			$parameter = $loopInfo->getCurrentParameter();
			$this->doAcceptContext($context, $parameter);
		} else {
			$this->doAccept($context);
		}
	}

	private function doAccept($context) {
		$expression = $this->getRemovePrefix();
		$parameter = $context->getArg($expression);
		$this->doAcceptContext($context, $parameter);
	}

	abstract public function doAcceptContext($context, $parameter);

	private function getRemovePrefix() {
		if (mb_strpos($this->expression, self::PREFIX_REPLACE_ONLY) === 0
				|| mb_strpos($this->expression, self::PREFIX_TERMINAL_DOT) === 0) {
			return mb_substr($this->expression, 2);
		} else if (mb_strpos($this->expression, self::PREFIX_NORMAL) === 0) {
			return mb_substr($this->expression, 1);
		} else {
			return $this->expression;
		}
	}	
}

