<?php
/**
 * Images Helper.
 *
 * @package    Silla
 * @subpackage Core\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Helpers;

use Core;
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;
use \Imagine\Image\ImageInterface;

/**
 * Class Image Helper definition.
 */
class Image
{
    /**
     * Generate one or more cropped thumbnails of the input image.
     *
     * @param string  $path_to_image Path to the input image file.
     * @param array   $thumbs_sizes  Array with needed thumb sizes it looks like: ['150x150', '240x320', '640x480'].
     * @param integer $quality       Image compression quality(only for JPG).
     *
     * @access public
     * @static
     * @throws \Exception Generic exception.
     *
     * @return void
     */
    public static function createThumbnailExact($path_to_image, array $thumbs_sizes, $quality = 95)
    {
        $meta = pathinfo($path_to_image);

        $imagine = new Imagine();
        $imagine = $imagine->open($path_to_image);

        foreach ($thumbs_sizes as $size) {
            preg_match_all('/(\d+)/', $size, $thumb_size);

            $size = new Box($thumb_size[0][0], $thumb_size[0][1]);
            $size_name = "{$thumb_size[0][0]}x{$thumb_size[0][1]}";

            $imagine = $imagine->thumbnail($size, ImageInterface::THUMBNAIL_INSET);
            $imagine->save(
                $meta['dirname'] . DIRECTORY_SEPARATOR . "{$size_name}-{$meta['filename']}.{$meta['extension']}",
                array('quality' => $quality)
            );
        }
    }

    /**
     * Generate one or more scaled thumbnails of the input image.
     *
     * @param string  $path_to_image Path to the input image file.
     * @param array   $thumbs_sizes  Array with needed thumb sizes it looks like: ['150x150', '240x320', '640x480'].
     * @param integer $quality       Image compression quality(only for JPG).
     *
     * @access public
     * @static
     * @throws \Exception Generic exception.
     *
     * @return void
     */
    public static function createThumbnailScaled($path_to_image, array $thumbs_sizes, $quality = 95)
    {
        $meta = pathinfo($path_to_image);

        $imagine = new Imagine();
        $imagine = $imagine->open($path_to_image);

        foreach ($thumbs_sizes as $size) {
            preg_match_all('/(\d+)/', $size, $thumb_size);

            $size = new Box($thumb_size[0][0], $thumb_size[0][1]);
            $size_name = "{$thumb_size[0][0]}x{$thumb_size[0][1]}";

            $imagine = $imagine->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND);
            $imagine->save(
                $meta['dirname'] . DIRECTORY_SEPARATOR . "{$size_name}-{$meta['filename']}.{$meta['extension']}",
                array('quality' => $quality)
            );
        }
    }

    /**
     * Calculates scale.
     *
     * @param integer $n     Parameter to be scaled.
     * @param integer $ratio Scale ratio.
     *
     * @access public
     * @static
     *
     * @return integer
     */
    public static function scaleSize($n, $ratio)
    {
        return floor($n * $ratio);
    }

    /**
     * Calculates image dimensions.
     *
     * @param string $path_to_image Image file path.
     *
     * @access public
     * @static
     *
     * @return array Example [width, height, aspect ratio].
     */
    public static function getDimensions($path_to_image)
    {
        $dimensions = getimagesize($path_to_image);

        return array(
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'aspect_ratio' => $dimensions[0] / $dimensions[1]
        );
    }
}
