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

namespace SSql\Sql;
	
use SSql\Sql\SqlTokenizer;
use SSql\Sql\Node\RootNode;
use SSql\Sql\Node\IfNode;
use SSql\Sql\Node\ForNode;
use SSql\Sql\Node\ElseNode;
use SSql\Sql\Node\LoopFirstNode;
use SSql\Sql\Node\LoopNextNode;
use SSql\Sql\Node\LoopLastNode;
use SSql\Sql\Node\LoopVariableNodeFactory;
use SSql\Exception\EndCommentNotFoundException;
use SSql\Exception\ForCommentConditionEmptyException;
use SSql\Exception\IfCommentConditionEmptyException;

/**
 * @author reimplement in PHP by amkt922 (originated in dbflute) 
 */
class SqlAnalyzer {
    
    private $sql = null;
    
    private $tokenizer = null;
    
    private $nodeStack = array();

	private $inBeginScope = false;

    /**
     * Constructor
     * @param string $sql
     */
    public function __construct($sql = '') {
        $this->sql = trim($sql);
        $this->tokenizer = new SqlTokenizer($sql);
    }
    
    public function analyze() {
    	array_push($this->nodeStack, $this->createRootNode());
        while (SqlTokenizer::EOF != $this->tokenizer->next()) {
            $this->parseToken();
        }
        return array_pop($this->nodeStack);
    }
    
    protected function createRootNode() {
		return new RootNode();			
    }

	protected function parseToken() {
        switch ($this->tokenizer->getTokenType()) {
        case SqlTokenizer::SQL:
            $this->parseSql();
            break;
        case SqlTokenizer::COMMENT:
            $this->parseComment();
            break;
        case SqlTokenizer::EL:
            $this->parseElse();
            break;
		default:
			break;
       }
	}

	protected function parseSql() {
		$node = $this->peekNodeStack();
		$sql = $this->tokenizer->getToken();
		if ($this->isConnectorAdjustable($node)) {
			$this->processSqlConnectorAdjustable($node, $sql);		
		} else {
			$node->addChild($this->createSqlNode($node, $sql));
		}
	}
	
	protected function processSqlConnectorAdjustable($node, $sql) {
		$st = new SqlTokenizer($sql);
		$st->skipWhitespace();
		$skippedToken = $st->skipToken();
		$st->skipWhitespace();

		if ($this->processSqlConnectorCondition($node, $st, $skippedToken)) {
			return;
		}

		$node->addChild($this->createSqlNode($node, $sql));	
	}

	protected function processSqlConnectorCondition($node, $st, $skippedToken) {
		if ($skippedToken === 'AND' || $skippedToken === 'and'
				|| $skippedToken === 'OR' || $skippedToken === 'or') {
			$node->addChild($this->createSqlConnectorNode($node, $st->getBefore(), $st->getAfter()));		
			return true;
		}	
		return false;
	}

	protected function createSqlConnectorNode($node, $connector, $sql) {
		if ($this->isNestedBegin($node)) {
			return Node\SqlConnectorNode::createSqlConnectorNodeAsIndependent($connector, $sql);
		} else {
			return Node\SqlConnectorNode::createSqlConnectorNode($connector, $sql);
		}
	}

	protected function createSqlNode($node, $sql) {
		if ($this->isNestedBegin($node)) {
			return Node\SqlNode::createSqlNodeAsIndependent($sql);
		} else {
			return Node\SqlNode::createSqlNode($sql);
		}
	}
	
    protected function isNestedBegin($node) {
        if (!($node instanceof BeginNode)) {
            return false;
        }
        return $node->isNested();
    }

	protected function isConnectorAdjustable($node) {
		if ($node->getChildSize() > 0) {
			return false;
		}

        return ($node instanceof Node\SqlConnectorAdjustable) 
					&& !$this->isTopBegin($node);
	}

    protected function isTopBegin($node) {
        if (!($node instanceof Node\BeginNode)) {
            return false;
        }
        return !$node->isNested();
    }

	protected function parseComment() {
		$token = $this->tokenizer->getToken();
		if ($this->isBeginComment($token)) {
			$this->parseBegin();
		} else if ($this->isIfComment($token)) {
			$this->parseIf();
		} else if ($this->isForComment($token)) {
			$this->parseFor();
		} else if ($this->isLoopVariableComment($token)) {
			$this->parseLoopVariable();
		} else if ($this->isEndComment($token)) {
			return;
		} else {
			$this->parseCommentVariable();
		}
	}

	protected function isBeginComment($comment) {
		return Node\BeginNode::MARK === $comment;
	}

	protected function parseBegin() {
		$beginNode = new Node\BeginNode();
		$this->inBeginScope = true;
		$this->peekNodeStack()->addChild($beginNode);
		array_push($this->nodeStack, $beginNode);
		$this->parseEnd();
		$this->inBeginScope = false;
	}

	protected function isIfComment($comment) {
		return mb_strpos($comment, Node\IfNode::PREFIX, 0) === 0;
	}

	protected function parseIf() {
        $comment = $this->tokenizer->getToken();
        $condition = trim(mb_substr($comment, mb_strlen(Node\IfNode::PREFIX)));
		if (empty($condition)) {
			throw new IfCommentConditionEmptyException();
		}
        $ifNode = new IfNode($condition, $this->sql);
        $this->peekNodeStack()->addChild($ifNode);
        array_push($this->nodeStack, $ifNode);
        $this->parseEnd();
	}

	protected function isForComment($comment) {
		return mb_strpos($comment, ForNode::PREFIX) === 0;
	}

	protected function parseFor() {
        $comment = $this->tokenizer->getToken();
        $condition = trim(mb_substr($comment, mb_strlen(Node\ForNode::PREFIX)));
		if (empty($condition)) {
			throw new ForCommentConditionEmptyException();
		}
        $forNode = new ForNode($condition, $this->sql);
        $this->peekNodeStack()->addChild($forNode);
        array_push($this->nodeStack, $forNode);
        $this->parseEnd();
	}

	protected function isLoopVariableComment($comment) {
		return mb_strpos($comment, LoopFirstNode::MARK) === 0 
				|| mb_strpos($comment, LoopNextNode::MARK) === 0 
				|| mb_strpos($comment, LoopLastNode::MARK) === 0;
	}

	public function parseLoopVariable() {
        $comment = $this->tokenizer->getToken();
		$spPos = mb_strpos($comment, ' ');
		// FIRST,LAST nodes has not condition.
		if ($spPos === false) {
        	$mark = $comment;
        	$condition = '';
		} else {
        	$mark = trim(mb_substr($comment, 0, $spPos));
        	$condition = mb_substr($comment, $spPos);
		}
		$loopVariableNode = LoopVariableNodeFactory::create($mark, $condition, $this->sql);	
        $this->peekNodeStack()->addChild($loopVariableNode);
		if (substr_count($condition, "'") < 2) {
			array_push($this->nodeStack, $loopVariableNode);
			$this->parseEnd();
		}
	}
	
	protected function isEndComment($comment) {
		return 'END' === $comment;
	}

	protected function parseElse() {
		$parent = $this->peekNodeStack();
		if (!($parent instanceof IfNode)) {
			return;
		}
		$ifNode = array_pop($this->nodeStack);
		$elseNode = new ElseNode();
		$ifNode->setElseNode($elseNode);
		array_push($this->nodeStack, $elseNode);	
		$this->tokenizer->skipWhitespace();
	}

	protected function parseEnd() {
        $commentType = SqlTokenizer::COMMENT;
        while (SqlTokenizer::EOF != $this->tokenizer->next()) {
            if ($this->tokenizer->getTokenType() == $commentType 
					&& $this->isEndComment($this->tokenizer->getToken())) {
                array_pop($this->nodeStack);
                return;
            }
            $this->parseToken();
        }
		throw new EndCommentNotFoundException();
	}

	private function peekNodeStack() {
		$node = $this->nodeStack[(count($this->nodeStack) - 1)];	
		return $node;
	}

	protected function parseCommentVariable() {
		$token = $this->tokenizer->getToken();
		$testValue = $this->tokenizer->skipToken(true);
		if (mb_strpos($token, "@") !== false) {
			$this->peekNodeStack()->addChild(new Node\EmbeddedVariableNode($token, $testValue));
		} else {
			$this->peekNodeStack()->addChild(new Node\BindVariableNode($token, $testValue));
		}
				
	}

}

