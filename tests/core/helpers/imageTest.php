<?php
/**
 * Image Helper Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Core\Helpers
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Core\Helpers;

use Core;
use Core\Helpers\Image;
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Core\Helpers\Image
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    protected static $vfs;
    protected static $rootPath;

    protected $imagePath;
    protected $width;
    protected $height;
    protected $size;
    protected $image;

    /**
     *  Setup virtual file system. Modify root path to point to the virtual file system.
     */
    public static function setUpBeforeClass()
    {
        self::$vfs = vfsStream::setup('root/');

        self::$rootPath = Core\Config()->paths('root');
        Core\Config()->modifyPath('root', vfsStream::url('root/'));
    }

    /**
     * Tear down virtual file system. Reset root path to point to the real file system.
     */
    public static function tearDownAfterClass()
    {
        self::$vfs = null;
        Core\Config()->modifyPath('root', self::$rootPath);
    }

    protected function setUp()
    {
        $this->imagePath = Core\Config()->paths('root') . 'image.png';
        $this->width     = 640;
        $this->height    = 480;
        $this->size      = array(
            "{$this->width}x{$this->height}",
            "{$this->height}x{$this->width}",
        );

        $imagine     = new Imagine();
        $this->image = $imagine
            ->create(new Box($this->width * 2, $this->width * 2))
            ->save($this->imagePath);
    }

    /**
     * @covers Core\Helpers\Image::createCroppedThumbnail
     */
    public function testCreatingCroppedThumbnail()
    {
        $thumbnails = Image::createCroppedThumbnail($this->imagePath, $this->size);
        foreach ($thumbnails as $thumbnail) {
            $this->assertFileExists($thumbnail);
        }
    }

    /**
     * @covers Core\Helpers\Image::createCroppedThumbnail
     */
    public function testCroppedThumbnailSizeMatches()
    {
        $thumbnails = Image::createCroppedThumbnail($this->imagePath, $this->size);
        for ($i = 0; $i < count($thumbnails); $i++) {
            /* Get expected image size. */
            preg_match_all('/(\d+)/', $this->size[$i], $expected);
            $expected = array_map('intval', $expected[0]);
            /* Get actual image size. */
            $actual = getimagesize($thumbnails[$i]);
            $actual = array_slice($actual, 0, 2);

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @covers Core\Helpers\Image::createScaledThumbnail
     */
    public function testCreatingScaledThumbnail()
    {
        $thumbnails = Image::createScaledThumbnail($this->imagePath, $this->size);
        foreach ($thumbnails as $thumbnail) {
            $this->assertFileExists($thumbnail);
        }
    }

    /**
     * @covers Core\Helpers\Image::getSize
     */
    public function testGettingSize()
    {
        $this->assertInternalType('array', Image::getSize($this->imagePath));
    }
}
