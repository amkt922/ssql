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

/**
 * @author reimplement in PHP and modified by amkt (originated in Java in dbflute) 
 */
class IfCommentEvaluator {

	const AND_OPE = " && ";
	const AND_OPE_EN = " and ";
	const OR_OPE = " || ";
	const OR_OPE_EN = " or ";
	const EQUAL_OPE = " == ";
	const GRATER_THAN_OPE = " > ";
	const LESS_THAN_OPE = " < ";
	const GREATER_EQUAL_OPE = " >= ";
	const LESS_EQUAL_OPE = " <= ";
	const NOT_EQUAL_OPE = " != ";
	const NOT_OPE = "!";
	const METHOD_SUFFIX_FIRST = "(";
	   
    private $condition = null;
    
    private $sql = null;

	private $parameterFinder = null;
    
    public function __construct($condition, $sql, $parameterFinder) {
		$this->condition = $condition;
		$this->sql = $sql;
		$this->parameterFinder = $parameterFinder;
    }

	public function evaluate() {
		if (strpos($this->condition, self::AND_OPE) !== false
				|| strpos($this->condition, self::AND_OPE_EN) !== false) {
			$this->condition = str_replace(self::AND_OPE_EN, self::AND_OPE, $this->condition);
			$clauses = mb_split(self::AND_OPE, $this->condition);
			foreach ($clauses as $clause) {
				$result = $this->evaluateClause($clause);
				if (!$result) {
					return false;
				}
			}
			return true;
		} else if (strpos($this->condition, self::OR_OPE) !== false
					|| strpos($this->condition, self::OR_OPE_EN) !== false) {
			$this->condition = str_replace(self::OR_OPE_EN, self::OR_OPE, $this->condition);
			$clauses = mb_split(self::OR_OPE, $this->condition);
			foreach ($clauses as $clause) {
				$result = $this->evaluateClause($clause);
				if ($result) {
					return true;
				}
			}
			return false;
		} else {
			return $this->evaluateClause($this->condition);
		}
	}

	private function evaluateClause($clause) {
		if (mb_strpos($clause, self::EQUAL_OPE) != false) {
			list($left, $right) = $this->leftRightClause(self::EQUAL_OPE, $clause);
			$leftValue = $this->parameterFinder->getParameter($left);
			$rightValue = $this->castIfNumeric($right);
			$rightValue = $this->convertNullIfNull($rightValue);
			$rightValue = $this->convertTrueOfFalseIfBool($rightValue);
			return $leftValue === $rightValue;
		} else if (mb_strpos($clause, self::GRATER_THAN_OPE) != false) {
			list($left, $right) = $this->leftRightClause(self::GRATER_THAN_OPE, $clause);
			$leftValue = $this->parameterFinder->getParameter($left);
			$rightValue = $this->castIfNumeric($right);
			return $leftValue > $rightValue;
		} else if (mb_strpos($clause, self::GREATER_EQUAL_OPE) != false) {
			list($left, $right) = $this->leftRightClause(self::GREATER_EQUAL_OPE, $clause);
			$leftValue = $this->parameterFinder->getParameter($left);
			$rightValue = $this->castIfNumeric($right);
			return $leftValue >= $rightValue;
		} else if (mb_strpos($clause, self::LESS_EQUAL_OPE) != false) {
			list($left, $right) = $this->leftRightClause(self::LESS_EQUAL_OPE, $clause);
			$leftValue = $this->parameterFinder->getParameter($left);
			$rightValue = $this->castIfNumeric($right);
			return $leftValue <= $rightValue;
		} else if (mb_strpos($clause, self::LESS_THAN_OPE) != false) {
			list($left, $right) = $this->leftRightClause(self::LESS_THAN_OPE, $clause);
			$leftValue = $this->parameterFinder->getParameter($left);
			$rightValue = $this->castIfNumeric($right);
			return $leftValue < $rightValue;
		} else if (mb_strpos($clause, self::NOT_EQUAL_OPE) != false) {
			list($left, $right) = $this->leftRightClause(self::NOT_EQUAL_OPE, $clause);
			$leftValue = $this->parameterFinder->getParameter($left);
			$rightValue = $this->castIfNumeric($right);
			$rightValue = $this->convertNullIfNull($rightValue);
			return $leftValue != $rightValue;
		} else {
			if (mb_strpos($clause, self::METHOD_SUFFIX_FIRST) != false) {
				return $this->parameterFinder->getParameter($clause);
			} else if (mb_strpos($clause, self::NOT_OPE) === 0) {
				$clause = mb_substr($clause, 1);
				$value = $this->parameterFinder->getParameter($clause);
				return $value ? false : true;
			} else {
				return (bool)$this->parameterFinder->getParameter($clause);
			}
		}
	}

	private function leftRightClause($operand, $clause) {
		return  mb_split($operand, $clause);
	}

	private function convertNullIfNull($value) {
		if ($value === 'null') {
			return null;
		}
		return $value;
	}

	private function convertTrueOfFalseIfBool($value) {
		if ($value === 'true') {
			return true;
		} else if ($value === 'false') {
			return false;
		} else {
			return $value;
		}
	}

	private function castIfNumeric($value) {
		if (is_numeric($value)) {
			$value = (int)$value;	
		} else if (is_float($value)) {
			$value = (float)$value;	
		} else if (is_double($value)) {
			$value = (double)$value;	
		}
		return $value;
	}
}

