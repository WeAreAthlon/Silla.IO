<?php
use Core\Helpers\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpmock\phpunit\PHPMock;

/**
 * @covers Core\Helpers\File
 */
class FileTest extends PHPUnit_Framework_TestCase
{
    use PHPMock;

    protected static $vfs;
    protected static $rootPath;
    protected $fullPath;
    protected $relativePath;
    protected $restrictedPath;
    protected $fileRoot;
    protected $moveUploadedFile;
    protected $uploadedFile;

    public static function setUpBeforeClass()
    {
        /* Setup virtual file system. */
        self::$vfs = vfsStream::setup('root/');

        /* Copy a plain text file for testing purposes. */
        copy(
            Core\Config()->paths('root') . 'VERSION',
            vfsStream::url('root/') . 'test.txt'
        );

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
        $this->fullPath = Core\Config()->paths('root') . 'temp/cache';
        $this->relativePath = 'temp/cache';
        $this->restrictedPath = '../malicious_script.php';
        $this->fileRoot = 'test';
        $this->baseName = 'test.txt';
        $this->uploadedFile = 'uploaded.txt';

        $_FILES = array(
            'test' => array(
                'name' => $this->baseName,
                'type' => 'text/plain',
                'size' => 2048,
                'tmp_name' => Core\Config()->paths('root') . $this->baseName,
                'error' => 0
            )
        );

        /* Mock built-in function move_uploaded_file for namespace Core\Helpers */
        $this->moveUploadedFile = $this->getFunctionMock('Core\Helpers', 'move_uploaded_file');
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
        $this->assertTrue(File::uploadedFileExists($this->fileRoot));
    }

    /**
     * @covers Core\Helpers\File::uploadedFileExists
     */
    public function testUploadedFileDoesNotExist()
    {
        unset($_FILES['test']['name']);
        $this->assertFalse(File::uploadedFileExists($this->fileRoot));
    }

    /**
     * @covers Core\Helpers\File::filterFilename
     */
    public function testFilteringFilenameWithExtension()
    {
        $this->assertEquals($_FILES['test']['name'], File::filterFilename($_FILES['test']['name']));
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
            File::formatFilename($this->fileRoot, $_FILES['test']['name'])
        );
    }

    /**
     * @covers Core\Helpers\File::validate
     * @expectedException InvalidArgumentException
     */
    public function testValidatingFileDoesNotExist()
    {
        $_FILES['test']['tmp_name'] = '';
        File::validate($_FILES['test'], array(), $_FILES['test']['size']);
    }

    /**
     * @covers Core\Helpers\File::validate
     */
    public function testValidatingBiggerThanAllowedFileSize()
    {
        $fileSize = 1;
        $this->assertFalse(File::validate($_FILES['test'], array(), $fileSize));
    }

    /**
     * @covers Core\Helpers\File::validate
     * @expectedException InvalidArgumentException
     */
    public function testValidatingNonExistentMimeType()
    {
        $mimeType = array('script');
        File::validate($_FILES['test'], $mimeType, $_FILES['test']['size']);
    }

    /**
     * @covers Core\Helpers\File::validate
     */
    public function testValidatingInvalidMimeType()
    {
        $mimeType = array('photo');
        $this->assertFalse(File::validate($_FILES['test'], $mimeType, $_FILES['test']['size']));
    }

    /**
     * @covers Core\Helpers\File::validate
     */
    public function testValidatingCompositeMimeType()
    {
        $mimeType = array('documents');
        $this->assertTrue(File::validate($_FILES['test'], $mimeType, $_FILES['test']['size']));
    }

    /**
     * @covers Core\Helpers\File::validate
     */
    public function testValidatingSimpleMimeType()
    {
        $mimeType = array('rtf');
        $this->assertTrue(File::validate($_FILES['test'], $mimeType, $_FILES['test']['size']));
    }

    /**
     * @covers Core\Helpers\File::upload
     */
    public function testUploadingWithoutSaveName()
    {
        /* Mock built-in function move_uploaded_file to return TRUE */
        $this->moveUploadedFile->expects($this->once())->willReturn(true);

        $this->assertTrue(
            File::upload(
                $_FILES['test'],
                Core\Config()->paths('root')
            )
        );
    }

    /**
     * @covers Core\Helpers\File::upload
     */
    public function testUploadingWithNonExistentDirectory()
    {
        /* Mock built-in function move_uploaded_file to return TRUE */
        $this->moveUploadedFile->expects($this->once())->willReturn(true);

        $this->assertTrue(
            File::upload(
                $_FILES['test'],
                Core\Config()->paths('root') . 'uploads',
                $this->uploadedFile
            )
        );
    }

    /**
     * @covers Core\Helpers\File::upload
     */
    public function testUploadingUnsuccessfully()
    {
        /* Mock built-in function move_uploaded_file to return FALSE */
        $this->moveUploadedFile->expects($this->once())->willReturn(false);

        $this->assertFalse(
            File::upload(
                $_FILES['test'],
                Core\Config()->paths('root'),
                $this->uploadedFile
            )
        );
    }

    /**
     * @covers Core\Helpers\File::putContents
     */
    public function testPuttingContentsInNonExistentDirectory()
    {
        $this->assertInternalType(
            'int',
            File::putContents($this->relativePath . $this->uploadedFile, $this->fileRoot)
        );
    }

    /**
     * @covers Core\Helpers\File::putContents
     */
    public function testPuttingContents()
    {
        $this->assertInternalType(
            'int',
            File::putContents($this->uploadedFile, $this->fileRoot)
        );
    }

    /**
     * @covers Core\Helpers\File::getContents
     * @expectedException InvalidArgumentException
     */
    public function testGettingContentsOfNonExistentFile()
    {
        $nonExistentFile = 'nofile.txt';
        File::getContents($nonExistentFile);
    }

    /**
     * @covers Core\Helpers\File::getContents
     */
    public function testGettingContents()
    {
        $this->assertStringEqualsFile(
            Core\Config()->paths('root') . $this->baseName,
            File::getContents($this->baseName)
        );
    }

    /**
     * @covers Core\Helpers\File::getContentsCurl
     * @todo Test with credentials.
     */
    public function testGettingContentsCurl()
    {
        $url = 'https://raw.githubusercontent.com/WeAreAthlon/silla.io/master/VERSION';
        $this->assertStringEqualsFile(
            Core\Config()->paths('root') . $this->baseName,
            File::getContentsCurl($url)
        );
    }

    /**
     * @covers Core\Helpers\File::copy
     */
    public function testCopyingFileInNonExistentDirectory()
    {
        $this->assertTrue(
            File::copy(
                $this->baseName,
                $this->relativePath . DIRECTORY_SEPARATOR . $this->uploadedFile
            )
        );
    }

    /**
     * @covers Core\Helpers\File::copy
     */
    public function testCopyingFile()
    {
        $this->assertTrue(File::copy($this->baseName, $this->uploadedFile));
    }

    /**
     * @covers Core\Helpers\File::delete
     * @expectedException InvalidArgumentException
     */
    public function testDeletingNonExistentFile()
    {
        $nonExistentFile = 'nofile.txt';
        File::delete($nonExistentFile);
    }

    /**
     * @covers Core\Helpers\File::delete
     */
    public function testDeletingFile()
    {
        $this->assertTrue(File::delete($this->baseName));
    }
}
