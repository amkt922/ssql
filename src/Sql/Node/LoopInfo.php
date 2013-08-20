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
class LoopInfo {
	protected $expression = null;

	protected $sql = null;

	protected $parameterList = array();

	protected $loopSize = 0;

	protected $loopIndex = 0;

	protected $parentLoop = null;

	public function getExpression() {
		return $this->expression;
	}

	public function setExpression($expression) {
		$this->expression = $expression;
	}

	public function getSql() {
		return $this->sql;
	}

	public function setSql($sql) {
		$this->sql = $sql;
	}

	public function getParameterList() {
		return $this->parameterList;
	}

	public function setParameterList($parameterList) {
		$this->parameterList = $parameterList;
	}

	public function getLoopSize() {
		return $this->loopSize;
	}

	public function setLoopSize($loopSize) {
		$this->loopSize = $loopSize;
	}

	public function getLoopIndex() {
		return $this->loopIndex;
	}

	public function setLoopIndex($loopIndex) {
		$this->loopIndex = $loopIndex;
	}

	public function getParentLoop() {
		return $this->parentLoop;
	}

	public function setParentLoop($parentLoop) {
		$this->parentLoop = $parentLoop;
	}

	public function getCurrentParameter() {
		return $this->parameterList[$this->loopIndex];
	}
}

