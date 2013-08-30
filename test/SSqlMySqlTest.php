<?php

namespace SSql;

require_once dirname(dirname(__FILE__)) . "/src/SSql.php";

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-30 at 07:47:07.
 */
class SSqlMySqlTest extends \PHPUnit_Framework_TestCase {

    private $ssql = null;

	/**
	 * @var array
	 */
	private $config = array('database' 
			=> array('driver' => 'Mysql'
					, 'dsn' => MYSQL_DSN
					, 'user' => MYSQL_USER
					, 'password' => MYSQL_PASSWORD)
			, 'sqlDir' => './sql/');

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 * @group mysql
	 */
	public static function setUpBeforeClass() {
		$database = MYSQL_DSN;
		$pdo = new \PDO(MYSQL_DSN, MYSQL_USER, MYSQL_PASSWORD);
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
        $pdo = null;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 * @group mysql
	 */
	public static function tearDownAfterClass() {
	}


    /*
     * @group mysql
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
	 * @group mysql
	 */
	public function test1() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()
			->selectList('selectUser', array());
		$this->assertSame(count($users), 5);
	}

	/**
	 * @covers SSql\SSql::from
	 * @todo   Implement testFrom().
	 * @group mysql
	 */
	public function test2() {
        $ssql = $this->ssql;
		$users = $ssql->createSSql()
					->selectList('selectUser', array('id' => 3));		
		$this->assertSame($users[0]['name'], 'takahashi');
	}

	/**
	 * 
	 * @group mysql
	 */
	public function test3() {
        $ssql = $this->ssql;
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
