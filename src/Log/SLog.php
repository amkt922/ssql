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

namespace SSql\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Logger class 
 * @author amkt922 
 */
class SLog {

    const LOGGER_NAME = 'SSql';

    const LOG_FILE_NAME = 'SSql_%s.log';

    const LOG_DIR = './';
    
    /**
     * @var \Monolog\Logger
     */
    private static $logger = null;

    /**
     * constructor
     */
    private function __construct() {}

    /**
     * create logger
     * @param array $config 
     */
    public static function createLogger(array $config = array()) {
        if (self::$logger === null) {
            if (empty($config)) {
                $logFileName = sprintf(self::LOG_FILE_NAME, date('YYYYmmdd'));
                self::$logger = new Logger(self::LOGGER_NAME);
                self::$logger->pushHandler(new StreamHandler(self::LOG_DIR.$logFileName, Logger::DEBUG));
            } else {
                $logFileName = explode('.', $config['file']);
                $logFileName = $logFileName[0] . '_' .  date('YYYYmmdd') . '.'. $logFileName[1];
                self::$logger = new Logger($config['name']);
                self::$logger->pushHandler(new StreamHandler($config['dir'] . $logFileName
                                                , self::toMonologLevel($config['level'])));
            }
        }
    }

    public static function getLogger() {
        return self::$logger;
    }

    public function info($message = '') {
        self::$logger->info($message);
    }

    public function error($message = '') {
        self::$logger->error($message);
    }

    public function debug($message = '') {
        self::$logger->debug($message);
    }

    private static function toMonologLevel($level) {
        $level = Logger::DEBUG;
        switch ($level) {
        case 'alert' :
            return Logger::ALERT;
            break;
        case 'critical' :
            return Logger::CRITICAL;
            break;
        case 'debug' :
            return Logger::DEBUG;
            break;
        case 'emergency' :
            return Logger::EMERGENCY;
            break;
        case 'error' :
            return Logger::ERROR;
            break;
        case 'info' :
            return Logger::INFO;
            break;
        case 'notice' :
            return Logger::NOTICE;
            break;
        case 'warning' :
            return Logger::WARNING;
            break;
        default:
            return Logger::DEBUG;
            break;
        }
    }
}

