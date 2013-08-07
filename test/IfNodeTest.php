<?php

namespace SSql\Sql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-07-05 at 10:09:50.
 */
class IfNodeTest extends \PHPUnit_Framework_TestCase {

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
		$sql = "/*IF a != null*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 4999);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame('a = 4999', $testSql);	
   }

    public function test2() {
		$sql = "/*IF a == null*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => null);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame('a = null', $testSql);	
   }

    public function test3() {
		$sql = "/*IF a == null*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 'hoge');
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

    public function test4() {
		$sql = "/*IF a != null*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => null);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test5() {
		$sql = "/*IF a > 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 100);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test6() {
		$sql = "/*IF a > 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 101);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = 101", $testSql);	
   }

   public function test7() {
		$sql = "/*IF a >= 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 100);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = 100", $testSql);	
   }

   public function test8() {
		$sql = "/*IF a >= 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 99);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test9() {
		$sql = "/*IF a < 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 100);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test10() {
		$sql = "/*IF a < 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 99);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = 99", $testSql);	
   }

   public function test11() {
		$sql = "/*IF a <= 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 100);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = 100", $testSql);	
   }

   public function test12() {
		$sql = "/*IF a <= 100*/a = /*@a*/1/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => 101);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test13() {
		$sql = "/*IF a */a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => true);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = true", $testSql);	
   }

   public function test14() {
		$sql = "/*IF a */a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => false);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test15() {
		$sql = "/*IF !a */a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => true);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test16() {
		$sql = "/*IF !a */a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => false);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = false", $testSql);	
   }

   public function test17() {
		$sql = "/*IF a == true*/a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => true);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = true", $testSql);	
   }

   public function test18() {
		$sql = "/*IF a == true*/a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => false);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test19() {
		$sql = "/*IF a == false*/a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => true);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test20() {
		$sql = "/*IF a == false*/a = /*@a*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = array('a' => false);
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("a = false", $testSql);	
   }

   public function test21() {
		$sql = "/*IF a.isPaging(1)*/b = /*@b*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = new Pmb();
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame("b = 300", $testSql);	
   }
   
   public function test22() {
		$sql = "/*IF a.isPaging(100)*/b = /*@b*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = new Pmb();
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

   public function test23() {
		$sql = "/*IF a.isPaging('hoge')*/b = /*@b*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = new Pmb();
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame('b = 300', $testSql);	
   }

   public function test24() {
		$sql = "/*IF a.isPaging('foo')*/b = /*@b*/100/*END*/";
		$an = new SqlAnalyzer($sql);
		$node = $an->analyze();
		$param = new Pmb();
		$context = Context\CommandContext::createCommandContext($param);
		$node->acceptContext($context);
		echo $testSql = $context->getSql();
		$this->assertSame(null, $testSql);	
   }

}

class Pmb {
	public function isPaging($param) {
		if ($param === 1 || $param === 'hoge') {
			return true;
		}
		return false;
	}
	public function getB() {
		return 300;
	}
}
