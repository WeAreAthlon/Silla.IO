<?php
use Core\Modules\Cache\Adapters\Database;
use Core\Modules\DB\Query;

/**
 * @covers Core\Modules\Cache\Adapters\Database
 */
class DatabaseTest extends PHPUnit_Framework_TestCase
{
    protected $databaseCache;
    protected $tableName;
    protected $fields;

    protected $key;
    protected $value;
    protected $expire;

    protected function setUp()
    {
        try {
            $this->databaseCache = new Database();
            $this->tableName = 'cache';
            $this->fields = array('cache_key', 'value', 'expire');

            $this->key = 'foo';
            $this->value = 'bar';
            $this->expire = 2;
        } catch (\Exception $e) {
            $this->markTestSkipped(
                'DB Connection is not available!'
            );
        }
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::store
     */
    public function testStoringKeyValuePair()
    {
        $query = new Query();
        $query
            ->remove()
            ->from($this->tableName)
            ->where("{$this->fields[0]} = ?", array(md5($this->key)))
            ->run();

        $this->assertTrue($this->databaseCache->store($this->key, $this->value));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::exists
     * @depends testStoringKeyValuePair
     */
    public function testKeyValuePairExists()
    {
        $this->assertTrue($this->databaseCache->exists($this->key));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::fetch
     * @depends testStoringKeyValuePair
     */
    public function testFetchingKeyValuePair()
    {
        $this->assertEquals($this->value, $this->databaseCache->fetch($this->key));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::remove
     * @depends testStoringKeyValuePair
     */
    public function testRemovingKeyValuePair()
    {
        $this->assertTrue($this->databaseCache->remove($this->key));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::store
     */
    public function testStoringArray()
    {
        $this->assertTrue($this->databaseCache->store($this->key, array($this->value)));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::fetch
     * @depends testStoringArray
     */
    public function testFetchingArray()
    {
        $this->assertEquals(array($this->value), $this->databaseCache->fetch($this->key));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::store
     */
    public function testUpdatingKeyValuePair()
    {
        $this->assertTrue($this->databaseCache->store($this->key, strrev($this->value)));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::store
     */
    public function testStoringKeyValuePairWithTimeout()
    {
        $this->assertTrue($this->databaseCache->store($this->key, $this->value, $this->expire));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::fetch
     * @depends testStoringKeyValuePairWithTimeout
     */
    public function testFetchingKeyValuePairWithTimeout()
    {
        $this->assertEquals($this->value, $this->databaseCache->fetch($this->key));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::fetch
     * @depends testStoringKeyValuePairWithTimeout
     */
    public function testFetchingExpiredKeyValuePair()
    {
        sleep(3);
        $this->assertNull($this->databaseCache->fetch($this->key));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::fetch
     */
    public function testFetchingNonexistentKeyValuePair()
    {
        $this->assertNull($this->databaseCache->fetch(strrev($this->key)));
    }

    /**
     * @covers Core\Modules\Cache\Adapters\Database::remove
     */
    public function testRemovingNonexistentKeyValuePair()
    {
        $this->assertFalse($this->databaseCache->remove(strrev($this->key)));
    }
}
