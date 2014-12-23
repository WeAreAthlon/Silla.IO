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
        vfsStream::setup('tmp');
        Core\Config()->modifyPath('tmp', vfsStream::url('tmp'));
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
