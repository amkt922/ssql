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

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
class EmbeddedVariableNode extends VariableNode {

	const PREFIX_NORMAL = '@';

	const PREFIX_REPLACE_ONLY = '@@';

	const PREFIX_TERMINAL_DOT = '@.';
    
	public function doAcceptContext($context) {
		$expression = $this->getRemovePrefix();
		$value = $context->getArg($expression);
		if (is_null($value)) {
			$context->addSql('null');
		} else if (is_bool($value)) {
			$context->addSql($value ? 'true' : 'false');
		} else if (mb_strpos($this->testValue, '\'') === 0) {
			$context->addSql('\'' . $value . '\'');
		} else {
			$value = $context->getArg($expression);
			if (is_array($value)) {
				if (mb_strpos($this->testValue, '\'') !== false) {
					$glue = '\',\'';
					$inClause = "'" . implode($glue, $value) . "'";
				} else {
					$glue = ',';
					$inClause = implode($glue, $value);
				}
				$context->addSql("({$inClause})");
			} else {
				$context->addSql($value);
			}
		}
		if (!is_null($this->testValue)) {
			if (mb_strpos($this->expression, self::PREFIX_REPLACE_ONLY) === 0) {
				$context->addSql($this->testValue);	
			} else if (mb_strpos($this->expression, self::PREFIX_TERMINAL_DOT) === 0) {
				$context->addSql(mb_substr($this->testValue, mb_strpos($this->testValue, '.')));	
			}
		}
	}

	private function getRemovePrefix() {
		if (mb_strpos($this->expression, self::PREFIX_REPLACE_ONLY) === 0
				|| mb_strpos($this->expression, self::PREFIX_TERMINAL_DOT) === 0) {
			return mb_substr($this->expression, 2);
		} else {
			return mb_substr($this->expression, 1);
		}
	}	
}

