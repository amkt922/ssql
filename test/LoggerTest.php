<?php

namespace SSql\Sql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";

use SSql\Log\SLog;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-07-05 at 10:09:50.
 */
class LoggerTest extends \PHPUnit_Framework_TestCase {

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
        SLog::createLogger();
        SLog::info('log');
        SLog::destroyLogger();
        $today = date('Ymd');
        $logFile = "SSql_{$today}.log";
        if (file_exists($logFile)) {
            $this->assertTrue(true);
            // remove log for next test
            unlink($logFile);
        } else {
            $this->assertTrue(false);
        }
   }

   public function test2() {
        // set logger
        $logConfig = array('name' => ''
                            , 'file' => 'test.log'
                            , 'dir' => '../'
                            , 'level' => 'info');
        $today = date('Ymd');
        SLog::createLogger($logConfig);
        SLog::info('log');
        SLog::destroyLogger();
        $logFile = "../test_{$today}.log";
        if (file_exists($logFile)) {
            $this->assertTrue(true);
            // remove log for next test
            unlink($logFile);
        } else {
            $this->assertTrue(false);
        }
   }


}
