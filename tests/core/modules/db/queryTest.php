<?php
use Core\Modules\DB;

class QueryTest extends PHPUnit_Framework_TestCase
{
    private $query;

    protected function setUp()
    {
        $this->query = new DB\Query();
    }

    public function testFind()
    {
        $this->query->find();
        $this->assertEquals('get', $this->query->type);
    }

    public function testCreate()
    {
        $this->query->create(DB\DBO::RECORD);
        $this->assertEquals('create', $this->query->type);
        $this->assertEquals('record', $this->query->object);

        $this->query->create('datasource');
        $this->assertEquals('create', $this->query->type);
        $this->assertEquals('datasource', $this->query->object);
    }

    public function testDelete()
    {
        $this->query->delete(DB\DBO::PROPERTY);
        $this->assertEquals('delete', $this->query->type);
        $this->assertEquals('property', $this->query->object);

        $this->query->delete('index');
        $this->assertEquals('delete', $this->query->type);
        $this->assertEquals('index', $this->query->object);
    }

    public function testChange()
    {
        $this->query->change(DB\DBO::RECORD);
        $this->assertEquals('change', $this->query->type);
        $this->assertEquals('record', $this->query->object);
    }

    public function testDatasource()
    {
        $this->query->datasource('tasks');
        $this->assertEquals('tasks', $this->query->datasource);
    }

    public function testFilter()
    {
        $filter = new DB\Filter('id', DB\FilterOp::EQ, 3);
        $this->query->filter($filter);
        $this->assertEquals($filter, $this->query->filter);
    }

    public function testSort()
    {
        $this->query->sort('id', 'desc');
        $this->query->sort('name', 'asc');
        $this->query->sort('description');

        $this->assertEquals(array('id', 'desc'), $this->query->sort[0]);
        $this->assertEquals(array('name', 'asc'), $this->query->sort[1]);
        $this->assertEquals(array('description', 'desc'), $this->query->sort[2]);
    }

    public function testSkip()
    {
        $this->query->skip(5);
        $this->assertEquals(5, $this->query->skip);

        $this->query->skip(13);
        $this->assertEquals(13, $this->query->skip);
    }

    public function testRand()
    {

    }

    public function testGet()
    {
        $this->query->get();
        $this->assertEquals(0, $this->query->get);

        $this->query->get(3);
        $this->assertEquals(3, $this->query->get);
    }

    public function testOne()
    {
        $this->query->one();
        $this->assertEquals(1, $this->query->get);
    }

    public function testAll()
    {
        $this->query->all();
        $this->assertEquals(0, $this->query->get);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIncompatibleObjectForCreate()
    {
        $this->query->create('Not an Object');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIncompatibleObjectForDelete()
    {
        $this->query->delete('category');
    }
}
