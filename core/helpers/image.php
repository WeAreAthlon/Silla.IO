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
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;
use \Imagine\Image\ImageInterface;

/**
 * Contains helper methods concerned with image manipulation.
 */
class Image
{
    /**
     * Generate one or more center cropped thumbnails given thumbnail sizes.
     *
     * @param string  $imagePath   Path to the input image file.
     * @param array   $thumbsSizes Array with thumb sizes. Example: ['150x150', '240x320', '640x480'].
     * @param integer $quality     Image compression quality (only for JPG and PNG).
     *
     * @uses self::createThumbnail To create a thumbnail from the given image.
     *
     * @return array Of paths to the newly created thumbnails.
     */
    public static function createCroppedThumbnail($imagePath, array $thumbsSizes, $quality = 95)
    {
        return self::createThumbnail($imagePath, $thumbsSizes, $quality, ImageInterface::THUMBNAIL_OUTBOUND);
    }

    /**
     * Generate one or more scaled thumbnails that fit in the given sizes.
     *
     * @param string  $imagePath   Path to the input image file.
     * @param array   $thumbsSizes Array with thumb sizes. Example: ['150x150', '240x320', '640x480'].
     * @param integer $quality     Image compression quality (only for JPG and PNG).
     *
     * @uses self::createThumbnail To create a thumbnail from the given image.
     *
     * @return array Of paths to the newly created thumbnails.
     */
    public static function createScaledThumbnail($imagePath, array $thumbsSizes, $quality = 95)
    {
        return self::createThumbnail($imagePath, $thumbsSizes, $quality, ImageInterface::THUMBNAIL_INSET);
    }

    /**
     * Gets image size and aspect ratio.
     *
     * @param string $imagePath Image file path.
     *
     * @return array With width, height, and aspect ratio.
     */
    public static function getSize($imagePath)
    {
        $dimensions = getimagesize($imagePath);

        return array(
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'ratio' => $dimensions[0] / $dimensions[1]
        );
    }

    /**
     * Generate one or more thumbnails of an input file.
     *
     * @param string  $imagePath   Path to the input image file.
     * @param array   $thumbsSizes Array with thumb sizes. Example: ['150x150', '240x320', '640x480'].
     * @param integer $quality     Image compression quality (only for JPG and PNG).
     * @param string  $mode        Used by thumbnail creation. Can be either inset or outbound.
     *
     * @return array Of paths to the newly created thumbnails.
     */
    private static function createThumbnail($imagePath, array $thumbsSizes, $quality, $mode)
    {
        $type = ($mode === 'inset') ? 'scaled' : 'cropped';

        $meta = pathinfo($imagePath);

        $imagine = new Imagine();
        $image = $imagine->open($imagePath);

        foreach ($thumbsSizes as $size) {
            preg_match_all('/(\d+)/', $size, $thumbSize);

            $size = new Box($thumbSize[0][0], $thumbSize[0][1]);
            $sizeName = "{$thumbSize[0][0]}x{$thumbSize[0][1]}";
            $filePath =
                $meta['dirname'] .
                DIRECTORY_SEPARATOR .
                "{$meta['filename']}_{$sizeName}_{$type}.{$meta['extension']}";

            $image
                ->thumbnail($size, $mode)
                ->save(
                    $filePath,
                    array('quality' => $quality)
                );
            /* Append the file path of the thumbnail for return. */
            $thumbnails[] = $filePath;
        }

        return $thumbnails;
    }
}
