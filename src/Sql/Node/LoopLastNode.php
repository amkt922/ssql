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

use SSql\Sql\Node\AbstractLoopNode;

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
class LoopLastNode extends AbstractLoopNode {
    
    const MARK = 'LAST';
    
    public function __construct($expression, $sql) {
		parent::__construct($expession, $sql);
    }

	public function isValid($loopSize, $loopIndex) {
		return $loopIndex === ($loopSize - 1);		
	}
}
