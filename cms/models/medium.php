<?php
/**
 * Medium Model.
 *
 * @package    Silla.IO
 * @subpackage CMS\Models
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
*/

namespace CMS\Models;

use Core;
use Core\Base;
use Core\Helpers;
use Core\Modules\DB\Decorators\Interfaces;

/**
 * Medium class definition.
 */
class Medium extends Base\Model implements Interfaces\TimezoneAwareness
{
    /**
     * Table storage name.
     *
     * @var string
     * @static
     * @access public
     */
    public static $tableName = 'media';

    /**
     * Whether the table we're looking at has a corresponding table, holding internationalized values.
     *
     * @var boolean
     * @access public
     * @static
     */
    public static $isI18n = true;

    /**
     * Asset file details.
     *
     * $_FILES[file] like array. The file should be already uploaded on the server.
     *
     * @var array
     * @access protected
     */
    protected $processedAssetFile;

    /**
     * Storage locations.
     *
     * @example Array of ['source': '', 'thumbnails': ''].
     *
     * @var array
     */
    protected $storage = array();

    /**
     * Constructor method.
     *
     * @param array $fields Array of fields and their values.
     */
    public function __construct(array $fields = array())
    {
        if (isset($fields['tmp_name']) && is_uploaded_file($fields['tmp_name'])) {
            $this->processedAssetFile = $fields;

            $cmsUser = Core\Registry()->get('current_user');
            $fileName = Helpers\Media::generateFileName($this->processedAssetFile);
            $fields = array(
                'cmsuser_id' => $cmsUser->id,
                'size'       => $this->processedAssetFile['size'],
                'filename'   => $fileName,
                'mimetype'   => Helpers\File::getMimeType($this->processedAssetFile),
                'title'      => $fileName,
            );
        }

        parent::__construct($fields);
    }

    /**
     * Retrieve the medium file.
     *
     * @return string Path to the file.
     */
    public function getFile()
    {
        $file = $this->storage['source'] . $this->filename;

        return is_file($file) ? $file : '';
    }

    /**
     * Retrieve image thumbnail.
     *
     * @param int     $width  Width of the thumbnail in pixes.
     * @param int     $height Height of the thumbnail in pixes.
     * @param boolean $exact  Whether to [resize] or [resize & crop] to the requested image dimensions.
     *
     * @uses getFile() To retrieve the source file.
     *
     * @return string Path to the file.
     */
    public function getThumbnail($width, $height, $exact = false)
    {
        $source = $this->getFile();
        $result = '';

        if ($source && 'photo' === Helpers\Media::getMediaType($this->mimetype)) {
            $medium = pathinfo($this->filename);
            $thumbnailFileName = "{$medium['filename']}-{$width}x{$height}.{$medium['extension']}";
            $thumbnail = $this->storage['thumbnails'] . $thumbnailFileName;

            if (!is_file($thumbnail)) {
                if ($exact) {
                    Core\Helpers\Image::crop($this->getFile(), $thumbnail, $width, $height);
                } else {
                    Core\Helpers\Image::resize($this->getFile(), $thumbnail, $width, $height);
                }
            }

            $result = $thumbnail;
        }

        return $result;
    }

    /**
     * Setup file storage paths.
     */
    public function afterCreate()
    {
        $this->storage['source'] = Core\Config()->getMediaStorageLocation() . Helpers\Media::getSavePath($this->id);
        $this->storage['thumbnails'] = $this->storage['source'] . 'thumbnails' . DIRECTORY_SEPARATOR;
    }

    /**
     * Validate medium file contents.
     */
    public function beforeValidate() {
        $limitations = array(
            'size' => Core\Helpers\File::getMaxUploadSize(),
            'type' => Core\Helpers\Media::getSupportedMediaTypes(),
        );

        if ($this->processedAssetFile) {
            if (!Helpers\File::isValidBySize($this->processedAssetFile, $limitations['size'])) {
                $this->errors['size'] = 'max_length_exceeded';
            }

            if (!Helpers\File::isValidByType($this->processedAssetFile, $limitations['type'])) {
                $this->errors['mimetype'] = 'invalid_format';
            }
        }
    }

    /**
     * Before validate Hook.
     *
     * @TODO extract media meta data.
     */
    public function beforeSave()
    {
        list($this->width, $this->height) = getimagesize($this->getFile());

        switch ($this->mimetype) {
            case 'image/jpeg':

                break;
            case 'image/gif':

                break;
        }
    }

    /**
     * Move the file to the storage path destination.
     */
    public function afterSave()
    {
        if (!$this->errors && $this->processedAssetFile) {
            $this->afterCreate();

            Helpers\File::upload($this->processedAssetFile, $this->storage['source'], $this->filename, true);

            if('photo' === Helpers\Media::getMediaType($this->mimetype)) {
                Helpers\Directory::create($this->storage['thumbnails']);
            }
        }
    }

    /**
     * Delete the asset from the storage.
     */
    public function afterDelete()
    {
        try {
            Helpers\Directory::delete($this->storage['source']);
        } catch (\Exception $e) {
        }
    }
}
