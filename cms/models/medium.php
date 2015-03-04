<?php
/**
 * Medium Model.
 *
 * @package    Silla.IO
 * @subpackage cms\Models
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
*/

namespace CMS\Models;

use Core;
use Core\Base;
use Core\Helpers;

/**
 * Medium class definition.
 */
class Medium extends Base\Model
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
     * Before validate Hook.
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
     * After validate Hook.
     */
    public function afterValidate()
    {
        if (!$this->errors && $this->processedAssetFile) {
            $storagePath = Core\Config()->getMediaStorageLocation() . Helpers\Media::getSavePath($this->id);

            /* Move the file to the storage path destination */
            if( !Helpers\File::upload($this->processedAssetFile, $storagePath, $this->filename, true)) {
                $this->errors['filename'] = 'denied';
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
        $storagePath = Core\Config()->getMediaStorageLocation() . Helpers\Media::getSavePath($this->id);
        $file = $storagePath . $this->filename;
        list($this->width, $this->height) = getimagesize($file);

        switch ($this->mimetype) {
            case 'image/jpeg':

                break;
            case 'image/gif':

                break;
        }
    }

    /**
     * After delete Hook.
     */
    public function afterDelete()
    {
        /* Delete the asset from the storage */
        try {
            $storagePath = Core\Config()->getMediaStorageLocation() . Helpers\Media::getSavePath($this->id);
            Helpers\Directory::delete($storagePath);
        } catch (\Exception $e) {
        }
    }
}
