<?php
/**
 * Redis Cache Adapter Module Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Modules\Cache\Adapters
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Modules\Cache\Adapters;

use Core\Modules\Cache\Adapters\Redis;

/**
 * @covers \Core\Modules\Cache\Adapters\Redis
 */
class RedisTest extends \PHPUnit_Framework_TestCase
{
    protected $redisCache;

    protected $key;
    protected $value;
    protected $expire;

    public static function setUpBeforeClass()
    {
        exec('redis-server &> /dev/null &');
    }

    public static function tearDownAferClass()
    {
        exec('redis-cli shutdown');
    }

    protected function setUp()
    {
        try {
            $this->redisCache = new Redis();
        } catch (\Exception $e) {
            $this->markTestSkipped(
                'Redis connection is not available!'
            );
        }

        $this->key    = 'foo';
        $this->value  = 'bar';
        $this->expire = 2;
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::store
     */
    public function testStoringKeyValuePair()
    {
        $this->assertTrue($this->redisCache->store($this->key, $this->value));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Redis::exists
     * @depends testStoringKeyValuePair
     */
    public function testKeyValuePairExists()
    {
        $this->assertTrue($this->redisCache->exists($this->key));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Redis::fetch
     * @depends testStoringKeyValuePair
     */
    public function testFetchingKeyValuePair()
    {
        $this->assertEquals($this->value, $this->redisCache->fetch($this->key));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Redis::remove
     * @depends testStoringKeyValuePair
     */
    public function testRemovingKeyValuePair()
    {
        $this->assertTrue((boolean)$this->redisCache->remove($this->key));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::store
     */
    public function testStoringArray()
    {
        $this->assertTrue($this->redisCache->store($this->key, array($this->value)));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Redis::fetch
     * @depends testStoringArray
     */
    public function testFetchingArray()
    {
        $this->assertEquals(array($this->value), $this->redisCache->fetch($this->key));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::store
     */
    public function testUpdatingKeyValuePair()
    {
        $this->assertTrue($this->redisCache->store($this->key, strrev($this->value)));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::store
     */
    public function testStoringKeyValuePairWithTimeout()
    {
        $this->assertTrue($this->redisCache->store($this->key, $this->value, $this->expire));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::store
     * @expectedException \Predis\Response\ServerException
     */
    public function testStoringKeyValuePairWithIncorrectTimeout()
    {
        $this->redisCache->store($this->key, $this->value, $this->value);
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Redis::fetch
     * @depends testStoringKeyValuePairWithTimeout
     */
    public function testFetchingKeyValuePairWithTimeout()
    {
        $this->assertEquals($this->value, $this->redisCache->fetch($this->key));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Redis::fetch
     * @depends testStoringKeyValuePairWithTimeout
     */
    public function testFetchingExpiredKeyValuePair()
    {
        sleep(3);
        $this->assertNull($this->redisCache->fetch($this->key));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::fetch
     */
    public function testFetchingNonexistentKeyValuePair()
    {
        $this->assertNull($this->redisCache->fetch(strrev($this->key)));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\Redis::remove
     */
    public function testRemovingNonexistentKeyValuePair()
    {
        $this->assertFalse((boolean)$this->redisCache->remove(strrev($this->key)));
    }
}
