<?php

namespace SSql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-30 at 07:47:07.
 */
class SSqlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SSql
	 */
	protected $object;
	protected $pdo;

	private $config = array('database' => array('dsn' => 'sqlite:./db/testdb.sqlite3')
								, 'sqlDir' => './sql/');

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public static function setUpBeforeClass() {
		$database = 'sqlite:./db/testdb.sqlite3';
		$pdo = new \PDO($database);
		$pdo->exec('drop table user');
		$create = <<<SQL
create table user (
	id			integer not null,
	name		varchar(30) not null,
	constraint user_pk primary key (id)
);
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
		$users = SSql::getSSql($this->config)
			->selectList('selectUser', array());		
		$this->assertSame(count($users), 5);
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 */
	public function test2() {
		$users = SSql::getSSql($this->config)
			->selectList('selectUser', array('id' => 3));		
		$this->assertSame($users[0]['name'], 'takahashi');
	}

	public function test3() {
		$users = SSql::getSSql($this->config)
			->selectList('selectUser', array('id' => 2), get_class(new User()));		
		$this->assertSame($users[0]->getId(), '2');
		$this->assertSame($users[0]->getName(), 'suzuki');
	}

	public function test4() {
		$users = SSql::getSSql($this->config)
			->selectList('selectUserSort'
							, array('paging' => true, 'idOrder' => 'desc')
							, get_class(new User()));		
		$this->assertSame($users[0]->getId(), '5');
		$this->assertSame($users[0]->getName(), 'ito');
	}

	public function test5() {
		$count = SSql::getSSql($this->config)
					->selectEntity('selectUserSort'
							, array('paging' => false));		
		$this->assertSame($count['count(id)'], '5');
	}

}

class User {
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