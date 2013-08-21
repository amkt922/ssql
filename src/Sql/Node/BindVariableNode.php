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

use SSql\Sql\Node\VariableNode;

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
class BindVariableNode extends VariableNode {
    
	public function doAcceptContext($context, $value) {
		if (mb_strpos($this->testValue, '\'') === 0) {
			$context->addSql('?', $value, 'string');
		} else {
			if (is_array($value)) {
				$questionValues = array_fill(0, count($value), '?');
				$inClause = implode(',', $questionValues);
				$type = 'integer';
				if (mb_strpos($this->testValue, '\'') !== false) {
					$type = 'string';
				}
				$context->addSql("({$inClause})", $value, $type);
			} else {
				$context->addSql('?', $value, gettype($value));
			}
		}
	}
}

