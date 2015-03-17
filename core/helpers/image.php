<?php
/**
 * Images Helper.
 *
 * @package    Silla.IO
 * @subpackage Core\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Helpers;

use Core;
use \Imagine;

/**
 * Class Image Helper definition.
 */
class Image
{
    /**
     * Crops an image.
     *
     * @param string $source  Path to the image source.
     * @param string $target  Path to the image thumbnail target location.
     * @param integer $width   Width of the newly created image.
     * @param integer $height  Height of the newly created image.
     * @param integer $quality Image compression quality. Applicable only for JPG.
     *
     * @return string Path to the cropped image.
     */
    public static function crop($source, $target, $width, $height, $quality = 85)
    {
        $imagine = new Imagine\Gd\Imagine();
        $mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;

        $size = new Imagine\Image\Box($width, $height);
        $imagine->open($source)->thumbnail($size, $mode)->save($target, array('quality' => $quality));

        return $target;
    }

    /**
     * Re-sizes an image.
     *
     * @param string $source  Path to the image source.
     * @param string $target  Path to the image thumbnail target location.
     * @param integer $width   Width of the newly created image.
     * @param integer $height  Height of the newly created image.
     * @param integer $quality Image compression quality. Applicable only for JPG.
     *
     * @return string Path to the cropped image.
     */
    public static function resize($source, $target, $width, $height, $quality = 85)
    {
        $imagine = new Imagine\Gd\Imagine();
        $mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;

        $size = new Imagine\Image\Box($width, $height);
        $imagine->open($source)->thumbnail($size, $mode)->save($target, array('quality' => $quality));

        return $target;
    }

    /**
     * Calculates image dimensions.
     *
     * @param string $filename Image file path.
     *
     * @access public
     * @static
     *
     * @return array Example [width, height, aspect ratio].
     */
    public static function getDimensions($filename)
    {
        $dimensions = getimagesize($filename);

        return array(
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'aspect_ratio' => $dimensions[0] / $dimensions[1]
        );
    }

    /**
     * Verify if an image is animated (GIF).
     *
     * An animated gif contains multiple "frames", with each frame having a header made up of:
     * - a static 4-byte sequence (\x00\x21\xF9\x04)
     * - 4 variable bytes
     * - a static 2-byte sequence (\x00\x2C)
     * We read through the file til we reach the end of the file, or we've found at least 2 frame headers.
     *
     * @param string $filename Image file path.
     *
     * @return boolean Whether the image is animated or not.
     */
    public static function is_animated($filename)
    {
        if (!($fh = @fopen($filename, 'rb'))) {
            return false;
        }

        $count = 0;

        while (!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); /* Read 100kb at a time */
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);
        }

        fclose($fh);

        return $count > 1;
    }
}
