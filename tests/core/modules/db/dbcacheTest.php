<?php
use Core\Modules\DB\DbCache;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @covers Core\Modules\DB\DbCache
 */
class DBCacheTest extends PHPUnit_Framework_TestCase
{
    protected $dbCache;
    protected $schemaMeta;

    public static function setUpBeforeClass()
    {
        /* Setup virtual file system. */
        vfsStream::setup('root/');
        /* Modify root path to point to the virtual file system. */
        Core\Config()->modifyPath('root', vfsStream::url('root/'));
    }

    /**
     * @covers Core\Modules\DB\DbCache::__construct()
     */
    public function testInitialisingDbCache()
    {
        $this->dbCache = new DbCache();
    }

    /**
     * @covers Core\Modules\DB\DbCache::__construct()
     */
    public function testInitialisingDbCacheWithCaching()
    {
        Core\Config()->CACHE['db_schema'] = true;
        $this->dbCache = new DbCache();
    }
}
