<?php
use Core\Modules\Cache\Cache;

/**
 * @covers Core\Modules\Cache\Cache
 * todo testing singletons not working
 */
class CacheTest extends PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $adapterName;
    protected $wrongAdapterName;

    protected function setUp()
    {
        $this->adapterName = 'FileSystem';
        $this->unsupportedAdapter = 'Unsupported';

        unset(Core\Registry()->cache);
        unset(Core\Cache()->cache);
    }

    /**
     * @covers Core\Modules\Cache\Cache::getInstance
     */
    public function testGettingInstantiatedCacheAdapter()
    {
        $this->adapter = Cache::getInstance($this->adapterName);
        $this->assertSame($this->adapter, Cache::getInstance($this->adapterName));
    }

    /**
     * @covers Core\Modules\Cache\Cache::getInstance
     */
    public function testInstantiatingUnsupportedCacheAdapter()
    {
        unset($this->adapter);
        Cache::getInstance($this->unsupportedAdapter);
    }
}
