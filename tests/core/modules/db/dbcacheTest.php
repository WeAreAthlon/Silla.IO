<?php
/**
 * Database Cache Module Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Modules\DB
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Modules\Cache\Adapters;

use Core;
use Core\Modules\DB\DbCache;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Core\Modules\DB\DbCache
 */
class DBCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $dbCache;
    protected $schemaMeta;

    /**
     * Setup virtual file system. Modify root path to point to the virtual file system.
     */
    public static function setUpBeforeClass()
    {
        vfsStream::setup('root/');
        Core\Config()->modifyPath('root', vfsStream::url('root/'));
    }

    /**
     * @covers \Core\Modules\DB\DbCache::__construct()
     */
    public function testInitialisingDbCache()
    {
        $this->dbCache = new DbCache(Core\Config()->DB);
    }

    /**
     * @covers \Core\Modules\DB\DbCache::__construct()
     */
    public function testInitialisingDbCacheWithCaching()
    {
        Core\Config()->CACHE['db_schema'] = true;
        $this->dbCache                    = new DbCache(Core\Config()->DB);
    }
}
