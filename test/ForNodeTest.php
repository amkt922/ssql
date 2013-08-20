<?php

namespace SSql\Sql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-07-05 at 10:09:50.
 */
class ForNodeTest extends \PHPUnit_Framework_TestCase {

    /**
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
	protected function setUp() {
  	}

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    public function test1() {
		$sql = "/*FOR userList*/username = /*#current*//*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 4999);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame('a = 4999', $testSql);	
   }

}