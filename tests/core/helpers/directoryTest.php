<?php
use Core\Helpers\Directory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpmock\phpunit\PHPMock;

/**
 * @covers Core\Helpers\Directory
 */
class DirectoryTest extends PHPUnit_Framework_TestCase
{
    use PHPMock;

    protected static $vfs;
    protected static $rootPath;
    protected $path;
    protected $dest;
    protected $file;
    protected $filePath;
    protected $mkDir;
    protected $rmDir;

    public static function setUpBeforeClass()
    {
        /* Setup virtual file system. */
        self::$vfs = vfsStream::setup('root/');

        self::$rootPath = Core\Config()->paths('root');
        /* Modify root path to point to the virtual file system. */
        Core\Config()->modifyPath('root', vfsStream::url('root/'));
    }

    public static function tearDownAfterClass()
    {
        /* Tear down virtual file system. */
        self::$vfs = null;
        /* Reset root path to point to the real file system. */
        Core\Config()->modifyPath('root', self::$rootPath);
    }

    protected function setUp()
    {
        $this->path = 'foo/bar';
        $this->dest = 'baz/boo';
        $this->file = 'test';
        $this->filePath = Core\Config()->paths('root') . $this->path . DIRECTORY_SEPARATOR . $this->file;
    }

    /**
     * @covers Core\Helpers\Directory::create
     * @expectedException DomainException
     */
    public function testCreatingExistingDirectory()
    {
        Directory::create(Core\Config()->paths('root'));
    }

    /**
     * @covers Core\Helpers\Directory::create
     */
    public function testCreatingDirectoryUnsuccessfully()
    {
        /* Mock built-in function mkdir for namespace Core\Helpers */
        $this->mkDir = $this->getFunctionMock('Core\Helpers', 'mkdir');
        $this->mkDir->expects($this->once())->willReturn(false);

        $this->assertFalse(Directory::create($this->path));
    }

    /**
     * @covers Core\Helpers\Directory::create
     */
    public function testCreatingDirectory()
    {
        $this->assertTrue(Directory::create($this->path));
    }

    /**
     * @covers Core\Helpers\Directory::copy
     */
    public function testCopyingDirectoryUnsuccessfully()
    {
        touch($this->filePath);

        /* Mock built-in function mkdir for namespace Core\Helpers */
        $this->mkDir = $this->getFunctionMock('Core\Helpers', 'mkdir');
        $this->mkDir->expects($this->once())->willReturn(false);

        $this->assertFalse(Directory::copy(Core\Config()->paths('root'), $this->dest));
    }

    /**
     * @covers Core\Helpers\Directory::copy
     */
    public function testCopyingDirectory()
    {
        $this->assertTrue(Directory::copy(dirname($this->path), $this->dest));
    }

    /**
     * @covers Core\Helpers\Directory::delete
     * @expectedException InvalidArgumentException
     */
    public function testDeletingDirectoryGivenInvalidArgument()
    {
        Directory::delete($this->filePath);
    }

    /**
     * @covers Core\Helpers\Directory::delete
     */
    public function testDeletingDirectoryUnsuccessfully()
    {
        /* Mock built-in function rmdir for namespace Core\Helpers */
        $this->rmDir = $this->getFunctionMock('Core\Helpers', 'rmdir');
        $this->rmDir->expects($this->any())->willReturn(false);

        $this->assertFalse(Directory::delete(dirname($this->path)));
    }

    /**
     * @covers Core\Helpers\Directory::delete
     */
    public function testDeletingDirectory()
    {
        $this->assertTrue(Directory::delete(dirname($this->path)));
    }
}
