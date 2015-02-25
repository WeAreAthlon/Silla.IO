<?php
use Core\Helpers\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @covers Core\Helpers\File
 */
class FileTest extends PHPUnit_Framework_TestCase
{
    protected $fullPath;
    protected $relativePath;
    protected $restrictedPath;
    protected $basename;

    public static function setUpBeforeClass()
    {
        vfsStream::setup('temp/');
        Core\Config()->modifyPath('tmp', vfsStream::url('temp/'));
    }

    protected function setUp()
    {
        $this->fullPath = Core\Config()->paths('root') . 'temp/cache';
        $this->relativePath = 'temp/cache';
        $this->restrictedPath = '../malicious_script.php';
        $this->basename = 'test';

        $_FILES = array(
            'test' => array(
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 542,
                'tmp_name' => $this->fullPath . '/source-test.jpg',
                'error' => 0
            )
        );
    }

    /**
     * @covers Core\Helpers\File::getFullPath
     */
    public function testGettingFullPathFromRelative()
    {
        $expectedPath = Core\Config()->paths('root') . $this->relativePath;
        $this->assertEquals($expectedPath, File::getFullPath($this->relativePath));
    }

    /**
     * @covers Core\Helpers\File::getFullPath
     */
    public function testGettingFullPathFromFull()
    {
        $this->assertEquals($this->fullPath, File::getFullPath($this->fullPath));
    }

    /**
     * @covers Core\Helpers\File::getRestrictedPath
     */
    public function testGettingRestrictedPathFromRelative()
    {
        $expectedPath = Core\Config()->paths('root') . $this->relativePath;
        $this->assertEquals($expectedPath, File::getRestrictedPath($this->relativePath));
    }

    /**
     * @covers Core\Helpers\File::getRestrictedPath
     */
    public function testGettingRestrictedPathFromRestricted()
    {
        $expectedPath = Core\Config()->paths('root') . $this->restrictedPath;
        $this->assertNotEquals($expectedPath, File::getRestrictedPath($this->restrictedPath));
    }

    /**
     * @covers Core\Helpers\File::uploadedFileExists
     */
    public function testUploadedFileExists()
    {
        $this->assertTrue(File::uploadedFileExists($this->basename));
    }

    /**
     * @covers Core\Helpers\File::uploadedFileExists
     */
    public function testUploadedFileDoesNotExist()
    {
        unset($_FILES['test']['name']);
        $this->assertFalse(File::uploadedFileExists($this->basename));
    }

    /**
     * @covers Core\Helpers\File::filterFilename
     */
    public function testFilteringFilenameWithExtension()
    {
        $this->assertEquals($_FILES['test']['name'], File::filterFilename($_FILES['test']['name']));
    }

    /**
     * @covers Core\Helpers\File::filterFilename
     */
    public function testFilteringFilenameWithoutExtension()
    {
        $this->markTestSkipped('The method does not work without file extension.');
        $this->assertEquals($_FILES['test']['name'], File::filterFilename($this->basename));
    }

    /**
     * @covers Core\Helpers\File::formatFilename
     */
    public function testFormattingFilenameWithExtension()
    {
        $this->assertEquals(
            $_FILES['test']['name'],
            File::formatFilename(
                $_FILES['test']['name'],
                $_FILES['test']['name']
            )
        );
    }

    /**
     * @covers Core\Helpers\File::formatFilename
     */
    public function testFormattingFilenameWithoutExtension()
    {
        $this->assertEquals(
            $_FILES['test']['name'],
            File::formatFilename($this->basename, $_FILES['test']['name'])
        );
    }
}
