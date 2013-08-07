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

namespace SSql\Sql;
	

use SSql\Sql\SqlTokenizer;
use SSql\Sql\Node\RootNode;
use SSql\Sql\Node\IfNode;
use SSql\Sql\Node\ElseNode;

/**
 * @author reimplement in PHP and modified by amkt (originated in dbflute) 
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
        $ifNode = new Node\IfNode($condition, $this->sql);
        $this->peekNodeStack()->addChild($ifNode);
        array_push($this->nodeStack, $ifNode);
        $this->parseEnd();
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
	}

	private function peekNodeStack() {
		$node = $this->nodeStack[(count($this->nodeStack) - 1)];	
		return $node;
	}

	protected function parseCommentVariable() {
		$token = $this->tokenizer->getToken();
		$testValue = $this->tokenizer->skipToken();
		if (mb_strpos($token, "@") !== false) {
			$this->peekNodeStack()->addChild(new Node\EmbeddedVariableNode($token, $testValue));
		} else {
			$this->peekNodeStack()->addChild(new Node\BindVariableNode($token, $testValue));
		}
				
	}

}

