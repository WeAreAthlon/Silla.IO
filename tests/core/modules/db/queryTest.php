<?php
use Tests\Core\Modules\DB\User;
use Core\Modules\DB;
use Core\Modules\DB\Query;
use Zob\Adapters\MySql\MySql;
use Zob\Objects\Table;

class QueryTest extends PHPUnit_Extensions_Database_TestCase
{
    protected static $connection;
    protected static $users;
    protected static $tasks;
    private $query;

    protected function getTableRows($queryTable)
    {
        $result = [];
        for($i = 0; $i < $queryTable->getRowCount(); $i++) {
            $result[] = $queryTable->getRow($i);
        }

        return $result;
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=silla_test', 'root');
        return $this->createDefaultDBConnection($pdo);
    }

    public function getDataSet()
    {
        return $this->createXmlDataSet('tests/core/modules/db/dataset.xml');
    }

    public static function setUpBeforeClass()
    {
        self::$connection = new MySql([
            'host' => 'localhost',
            'name' => 'silla_test',
            'user' => 'root',
            'password' => ''
        ]);

        self::$users = new Table(self::$connection, 'users', [
            [
                'name' => 'id',
                'type' => 'int',
                'length' => 10,
                'pk'    => true,
                'ai'    => true
            ],
            [
                'name' => 'name',
                'type' => 'varchar',
                'length' => 255
            ],
            [
                'name' => 'email',
                'type' => 'varchar',
                'length' => 255,
                'required' => true
            ],
            [
                'name' => 'created_at',
                'type' => 'datetime'
            ]
        ]);
        self::$users->create();

        self::$tasks = new Table(self::$connection, 'tasks', [
            [
                'name' => 'id',
                'type' => 'int',
                'length' => 10,
                'pk'    => true,
                'ai'    => true
            ],
            [
                'name' => 'title',
                'type' => 'varchar',
                'length' => 255
            ],
            [
                'name' => 'user_id',
                'type' => 'int',
                'length' => 10,
                'required' => true
            ],
            [
                'name' => 'description',
                'type' => 'text'
            ],
            [
                'name' => 'created_at',
                'type' => 'datetime'
            ]
        ]);
        self::$tasks->create();
    }

    public static function tearDownAfterClass()
    {
        self::$users->delete();
        self::$tasks->delete();
    }

    public function testSelect()
    {
        $q = (new Query(self::$connection))->select()->from('users');
        $items = $q->run();
        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users'
        );

        $this->assertEquals($items, $this->getTableRows($queryTable));
    }

    public function testSelectOnlyTheId()
    {
        $q = (new Query(self::$connection))->select('id')->from('users');
        $items = $q->run();
        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT id FROM users'
        );

        $this->assertEquals($items, $this->getTableRows($queryTable));
    }

    public function testSelectFromSubquery()
    {
        $q = (new Query(self::$connection))->select()->from('(SELECT * from users where id < ?) as d', [5]);
        $items = $q->run();
        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM (SELECT * from users where id < 5) as d'
        );

        $this->assertEquals($items, $this->getTableRows($queryTable));
    }

    public function testSelectWithLimit()
    {
        $q = (new Query(self::$connection))->select()->from('users')->limit(4);
        $items = $q->run();
        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users LIMIT 4'
        );

        $this->assertEquals($items, $this->getTableRows($queryTable));
    }

    public function testSelectWithLimitAndOffset()
    {
        $q = (new Query(self::$connection))->select()->from('users')->limit(2, 3);
        $items = $q->run();
        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT * FROM users LIMIT 2 OFFSET 3'
        );

        $this->assertEquals($items, $this->getTableRows($queryTable));
    }

    public function testSelectWithJoin()
    {
        $usr = new User();
        $q = (new Query(self::$connection))->select('users.*')->from('users')->joins('tasks');
        $items = $q->run();
        $queryTable = $this->getConnection()->createQueryTable(
            'users', 'SELECT users.* FROM users LEFT JOIN tasks ON(users.id = tasks.user_id)'
        );

        $this->assertEquals($items, $this->getTableRows($queryTable));
    }
}

