<?php
/**
 * Cache Module Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Modules\Cache
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Modules\Cache;

use Core\Modules\Cache\Cache;

/**
 * @covers \Core\Modules\Cache\Cache
 * @todo testing singletons not working
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $adapterName;
    protected $wrongAdapterName;
    protected $unsupportedAdapter;

    protected function setUp()
    {
        $this->adapterName        = 'FileSystem';
        $this->unsupportedAdapter = 'Unsupported';
    }

    /**
     * @covers \Core\Modules\Cache\Cache::getInstance
     */
    public function testGettingInstantiatedCacheAdapter()
    {
        $this->adapter = Cache::getInstance($this->adapterName);
        $this->assertSame($this->adapter, Cache::getInstance($this->adapterName));
    }

    /**
     * @covers \Core\Modules\Cache\Cache::getInstance
     */
    public function testInstantiatingUnsupportedCacheAdapter()
    {
        unset($this->adapter);
        Cache::getInstance($this->unsupportedAdapter);
    }
}
