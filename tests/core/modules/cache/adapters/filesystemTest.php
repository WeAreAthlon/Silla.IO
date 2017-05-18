<?php
/**
 * File System Cache Adapter Module Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Modules\Cache\Adapters
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Modules\Cache\Adapters;

use Core;
use Core\Modules\Cache\Adapters\FileSystem;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Core\Modules\Cache\Adapters\FileSystem
 */
class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    protected $fileSystemCache;
    protected $key;
    protected $value;
    protected $expire;

    /**
     * Setup virtual file system. Modify root path to point to the virtual file system.
     */
    public static function setUpBeforeClass()
    {
        vfsStream::setup('root/');
        Core\Config()->modifyPath('root', vfsStream::url('root/'));
    }

    protected function setUp()
    {
        $this->fileSystemCache = new FileSystem();
        $this->key             = 'foo';
        $this->value           = 'bar';
        $this->expire          = 2;
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\FileSystem::store
     */
    public function testStoringKeyValuePair()
    {
        $this->assertTrue((boolean)$this->fileSystemCache->store($this->key, $this->value));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\FileSystem::exists
     * @depends testStoringKeyValuePair
     */
    public function testKeyValuePairExists()
    {
        $this->assertTrue($this->fileSystemCache->exists($this->key));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\FileSystem::fetch
     * @depends testStoringKeyValuePair
     */
    public function testFetchingKeyValuePair()
    {
        $this->assertEquals($this->value, $this->fileSystemCache->fetch($this->key));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\FileSystem::remove
     * @depends testStoringKeyValuePair
     */
    public function testRemovingKeyValuePair()
    {
        $this->assertTrue($this->fileSystemCache->remove($this->key));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\FileSystem::store
     */
    public function testStoringArray()
    {
        $this->assertTrue((boolean)$this->fileSystemCache->store($this->key, array($this->value)));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\FileSystem::fetch
     * @depends testStoringArray
     */
    public function testFetchingArray()
    {
        $this->assertEquals(array($this->value), $this->fileSystemCache->fetch($this->key));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\FileSystem::store
     */
    public function testUpdatingKeyValuePair()
    {
        $this->assertTrue((boolean)$this->fileSystemCache->store($this->key, $this->value));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\FileSystem::store
     */
    public function testStoringKeyValuePairWithTimeout()
    {
        $this->assertTrue((boolean)$this->fileSystemCache->store($this->key, $this->value, $this->expire));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\FileSystem::fetch
     * @depends testStoringKeyValuePairWithTimeout
     */
    public function testFetchingKeyValuePairWithTimeout()
    {
        $this->assertEquals($this->value, $this->fileSystemCache->fetch($this->key));
    }

    /**
     * @covers  \Core\Modules\Cache\Adapters\Database::fetch
     * @depends testStoringKeyValuePairWithTimeout
     */
    public function testFetchingExpiredKeyValuePair()
    {
        sleep(3);
        $this->assertNull($this->fileSystemCache->fetch($this->key));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\FileSystem::fetch
     */
    public function testFetchingNonexistentKeyValuePair()
    {
        $this->assertNull($this->fileSystemCache->fetch(strrev($this->key)));
    }

    /**
     * @covers \Core\Modules\Cache\Adapters\FileSystem::remove
     */
    public function testRemovingNonexistentKeyValuePair()
    {
        $this->assertFalse($this->fileSystemCache->remove(strrev($this->key)));
    }
}
