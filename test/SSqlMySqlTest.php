<?php

namespace SSql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-30 at 07:47:07.
 */
class SSqlMySqlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SSql
	 */
	protected $object;
	protected $pdo;

	private $config = array('database' 
			=> array('dsn' => 'mysql:host=localhost;dbname=ssql_test'
					, 'user' => 'root'
					, 'password' => 'admin')
			, 'sqlDir' => './sql/');

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public static function setUpBeforeClass() {
		$database = 'mysql:host=localhost;dbname=ssql_test';
		$pdo = new \PDO($database, 'root', 'admin');
		$pdo->exec('drop table user');
		$create = <<<SQL
CREATE TABLE `user` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(45) DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8
SQL;
		$pdo->exec($create);
		$insert = <<<SQL
insert into user values(1, 'sato');
insert into user values(2, 'suzuki');
insert into user values(3, 'takahashi');
insert into user values(4, 'tanaka');
insert into user values(5, 'ito');
SQL;
		$pdo->exec($insert);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	public static function tearDownAfterClass() {
		
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 */
	public function test1() {
		$ssql = SSql::connect($this->config);
		$users = $ssql->createSSql()
			->selectList('selectUser', array());		
		$this->assertSame(count($users), 5);
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 */
	public function test2() {
		$ssql = SSql::connect($this->config);
		$users = $ssql->createSSql()
					->selectList('selectUser', array('id' => 3));		
		$this->assertSame($users[0]['name'], 'takahashi');
	}

	public function test3() {
		$ssql = SSql::connect($this->config);
		$users = $ssql->createSSql()
			->selectList('selectUser', array('id' => 2), get_class(new MysqlUser()));		
		$this->assertSame($users[0]->getId(), '2');
		$this->assertSame($users[0]->getName(), 'suzuki');
	}

}

class MysqlUser {
	private $id;

	private $name;
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}


}