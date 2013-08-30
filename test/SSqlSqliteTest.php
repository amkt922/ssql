<?php

namespace SSql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";

use \PDO;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-30 at 07:47:07.
 */
class SSqlSqliteTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SSql
	 */
	protected $ssql;

	private $config = array('database' => array('driver' => 'Sqlite', 'dsn' => SQLITE_DSN)
								, 'sqlDir' => './sql/');

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 * @group sqlite
	 */
	public static function setUpBeforeClass() {
		$pdo = new PDO(SQLITE_DSN);
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
	 * @group sqlite
	 */
	public static function tearDownAfterClass() {
		
	}

	/**
	 * @group sqlite
	 * 
	 */
	protected function setUp() {
		$this->config['sqlDir'] = __DIR__ . "/" . $this->config['sqlDir'];
        $this->ssql = SSql::connect($this->config);
	}

    protected function tearDown() {
        $this->ssql->close();
    }
	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 * @group sqlite
	 */
	public function test1() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()->selectList('selectUser', array());
		$this->assertSame(count($users), 5);
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 * @group sqlite
	 */
	public function test2() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()->selectList('selectUser', array('id' => 1));
		$this->assertSame($users[0]['name'], 'sato');
	}

	/**
	 * @group sqlite
	 * 
	 */
	public function test3() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()->selectList('selectUser', array('id' => 2), get_class(new User()));
		$this->assertSame($users[0]->getId(), '2');
		$this->assertSame($users[0]->getName(), 'suzuki');
	}

	/**
	 * @group sqlite
	 * 
	 */
	public function test4() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()->selectList('selectUserSort'
							, array('paging' => true, 'idOrder' => 'desc')
							, get_class(new User()));		
		$this->assertSame($users[0]->getId(), '5');
		$this->assertSame($users[0]->getName(), 'ito');
	}

	/**
	 * @group sqlite
	 * 
	 */
	public function test5() {
        $ssql = $this->ssql;
		$count = $ssql->createSSql()->selectEntity('selectUserSort'
							, array('paging' => false));		
		$this->assertSame($count['count(id)'], '5');
	}

	/**
	 * @group sqlite
	 * 
	 */
	public function test6() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select('*')->from('user')->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
	}


	/**
	 * @group sqlite
	 * 
	 */
	public function test7() {
        $ssql = $this->ssql;
		$ssql->createSQry()->update('User')->set(array('name' => 'kato'))
					->where(array('id =' => 1))->execute();
		$users = $ssql->createSSql()->selectList('selectUser', array('id' => 1), get_class(new User()));		
		$this->assertSame($users[0]->getId(), '1');
		$this->assertSame($users[0]->getName(), 'kato');
	}

	/**
	 * 
	 * @group sqlite
	 */
	public function test7_1() {
		// back to original value.
        $ssql = $this->ssql;
		$ssql->createSQry()->update('User')->set(array('name' => 'sato'))
					->where(array('id =' => 1))->execute();
	}

	/**
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 * @group sqlite
	 */
	public function test8() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->where(array('name like' => 'sato'))->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
	}

	/**
	 * @group sqlite
	 * 
	 */
	public function test9() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()
					->select(array('id', 'name'))
					->from('User')
					->where(array('id =' => 1))->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test10() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->selectDistinct(array('id', 'name'))
						->from('User')
						->where(array('id =' => 2))->execute();
		$this->assertSame($users[0]['id'], '2');
		$this->assertSame($users[0]['name'], 'suzuki');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test11() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->where(array('id =' => 3))
						->andWhere(array('name like' => 'takahashi'))->execute();
		$this->assertSame($users[0]['id'], '3');
		$this->assertSame($users[0]['name'], 'takahashi');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test12() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->where(array('id =' => 2))
						->orWhere(array('id =' => 3))->execute();
		$this->assertSame($users[0]['id'], '2');
		$this->assertSame($users[0]['name'], 'suzuki');
		$this->assertSame($users[1]['id'], '3');
		$this->assertSame($users[1]['name'], 'takahashi');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test13() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->limit(2)
						->offset(1)->execute();
		$this->assertSame($users[0]['id'], '2');
		$this->assertSame($users[0]['name'], 'suzuki');
		$this->assertSame($users[1]['id'], '3');
		$this->assertSame($users[1]['name'], 'takahashi');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test14() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->where(array('id IN' => array(1,2)))->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
		$this->assertSame($users[1]['id'], '2');
		$this->assertSame($users[1]['name'], 'suzuki');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test15() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->where(array('id IN' => array(1)))
						->execute(get_class(new User));
		$this->assertSame($users[0]->getId(), '1');
		$this->assertSame($users[0]->getName(), 'sato');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test16() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->orderBy(array('id' => 'desc'))
						->execute();
		$this->assertSame($users[0]['id'], '5');
		$this->assertSame($users[0]['name'], 'ito');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test17() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->groupBy(array('id'))
						->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test18() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->groupBy(array('id'))
						->having(array('id =' => 3))
						->execute();
		$this->assertSame($users[0]['id'], '3');
		$this->assertSame($users[0]['name'], 'takahashi');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test19() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->groupBy(array('id'))
						->having(array('id =' => 1))
						->andHaving(array('name like' => 'sato'))
						->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test20() {
        $ssql = $this->ssql;
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->groupBy(array('id'))
						->having(array('id =' => 1))
						->orHaving(array('id =' => 2))
						->execute();
		$this->assertSame($users[0]['id'], '1');
		$this->assertSame($users[0]['name'], 'sato');
		$this->assertSame($users[1]['id'], '2');
		$this->assertSame($users[1]['name'], 'suzuki');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test21() {
        $ssql = $this->ssql;
		$ssql->createSQry()->insert()
						->into('User', array('id', 'name'))
						->values(array(array(6, 'tanaka')))
						->execute();
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->execute();
		$this->assertSame($users[5]['id'], '6');
		$this->assertSame($users[5]['name'], 'tanaka');
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test22() {
        $ssql = $this->ssql;
		$ssql->createSQry()->delete()
						->from('User')
						->where((array('id =' => 6)))
						->execute();
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->execute();
		$this->assertSame(count($users), 5);
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 * @group sqlite
	 */
	public function test23() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()->selectList('selectUserFor'
												, array('idList' => array(2,3,4)));		
		$this->assertSame($users[0]['name'], 'suzuki');
		$this->assertSame($users[1]['name'], 'takahashi');
		$this->assertSame($users[2]['name'], 'tanaka');
	}


	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test24() {
        $ssql = $this->ssql;
		$ssql->beginTransaction();
		$ssql->createSQry()->insert()
						->into('User', array('id', 'name'))
						->values(array(array(6, 'tanaka')))
						->execute();
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->execute();
		$this->assertSame(count($users), 6);
		$ssql->rollback();
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->execute();
		$this->assertSame(count($users), 5);
	}

	/**
	 * @group sqlite
	 * @covers SSql\SQueryManager::select
	 * @todo   Implement testSelect().
	 */
	public function test25() {
        $ssql = $this->ssql;
		$ssql->beginTransaction();
		$ssql->createSQry()->insert()
						->into('User', array('id', 'name'))
						->values(array(array(6, 'tanaka')))
						->execute();
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->execute();
		$this->assertSame(count($users), 6);
		$ssql->commit();
		$users = $ssql->createSQry()->select(array('id', 'name'))
						->from('User')
						->execute();
		$this->assertSame(count($users), 6);
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