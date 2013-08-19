<?php

namespace SSql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-30 at 07:47:07.
 */
class SSqlPgsqlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SSql
	 */
	protected $object;
	protected $pdo;

	/**
	 * @var type 
	 */
	private $config = array('database' 
			=> array('driver' => 'Postgres'
					, 'dsn' => PGSQL_DSN
					, 'user' => PGSQL_USER
					, 'password' => PGSQL_PASSWORD)
			, 'sqlDir' => './sql/');

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 * @group pgsql
	 */
	public static function setUpBeforeClass() {
		$database = PGSQL_DSN;
		$pdo = new \PDO(PGSQL_DSN, PGSQL_USER, PGSQL_PASSWORD);
		$pdo->exec('drop table user');
		$create = <<<SQL
CREATE TABLE `user` (
   `id` integer PRIMARY KEY,
   `name` varchar(45) NOT NULL,
 )
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
	 * @group pgsql
	 */
	public static function tearDownAfterClass() {
		
	}

	/*
	 * @group pgsql
	 * 
	 */
	protected function setUp() {
		$this->config['sqlDir'] = __DIR__ . "/" . $this->config['sqlDir'];
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 * @group pgsql
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
	 * @group pgsql
	 */
	public function test2() {
		$ssql = SSql::connect($this->config);
		$users = $ssql->createSSql()
					->selectList('selectUser', array('id' => 3));		
		$this->assertSame($users[0]['name'], 'takahashi');
	}

	/**
	 * 
	 * @group pgsql
	 */
	public function test3() {
		$ssql = SSql::connect($this->config);
		$users = $ssql->createSSql()
			->selectList('selectUser', array('id' => 2), get_class(new PgsqlUser()));		
		$this->assertSame($users[0]->getId(), '2');
		$this->assertSame($users[0]->getName(), 'suzuki');
	}

}

class PgsqlUser {
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
