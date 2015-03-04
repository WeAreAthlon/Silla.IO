<?php
/**
 * Media Management Helper.
 *
 * @package    Silla.IO
 * @subpackage Core\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Helpers;

use Core;
use CMS\Models;

/**
 * Media Manager Helper Class definition.
 */
class Media
{
    /**
     * Generates a Unique file name.
     *
     * @param array  $file   Standard array representation of $_FILES array.
     *
     * @uses CMS\Models\Medium                   Querying the storage engine.
     * @uses Core\Helpers\File::filterFileName() Filtering the file name.
     *
     * @return string The generated file name.
     */
    public static function generateFileName(array $file) {
        $fileName = Core\Helpers\File::filterFileName($file['name']);

        if (Models\Medium::find()->where('filename = ?', array($fileName))->first()) {
            $fileMeta = pathinfo($fileName);
            $copyCounter = 2;

            do {
                $fileName = $fileMeta['filename'] . "({$copyCounter})." . $fileMeta['extension'];
                $copyCounter++;
            } while (Models\Medium::find()->where('filename = ?', array($fileName))->first());
        }

        return $fileName;
    }

    /**
     * Generates relative path to the save location.
     *
     * The function generates unique save path for each call.
     *
     * @param integer $id Unique identifier for the medium.
     *
     * @return string Relative path with ending trailing directory separator.
     */
    public static function getSavePath($id)
    {
        $directory = str_pad((int)($id / 1000) + 1, 4, '0', STR_PAD_LEFT);

        return $directory . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
    }

    /**
     * Calculates the supported Media file types.
     *
     * @uses Core\Helpers\YAML::get() To fetch the configured Media Types.
     *
     * @return array List of supported Media types.
     */
    public static function getSupportedMediaTypes()
    {
        $mediaRestrictions = Core\Helpers\YAML::get('restrictions', 'media');

        return $mediaRestrictions['type'];
    }

    /**
     * Calculates the supported file MIME types.
     *
     * @uses Core\Helpers\File::getMimeTypesForMediaType()
     *
     * @return array List of supported MIME types.
     */
    public static function getSupportedMimeTypes()
    {
        $supportedMimeTypes = array();
        $supportedMediaTypes = self::getSupportedMediaTypes();

        foreach ($supportedMediaTypes as $mediaType) {
            $supportedMimeTypes = array_merge($supportedMimeTypes, Core\Helpers\File::getMimeTypesForMediaType($mediaType));
        }

        return $supportedMimeTypes;
    }
}
