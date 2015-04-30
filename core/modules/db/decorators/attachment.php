<?php
/**
 * Attachment Decorator.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators;

use Core;
use Core\Base;
use Core\Helpers;
use Core\Modules\DB\Interfaces;

/**
 * Class Attachment Decorator Implementation definition.
 */
abstract class Attachment implements Interfaces\Decorator
{
    /**
     * Attachments container.
     *
     * @var array
     * @static
     */
    private static $attachments = array();

    /**
     * Current attachments container.
     *
     * @var array
     * @static
     */
    private static $attachmentsOld = array();

    /**
     * Whether the attachment is currently uploading.
     *
     * @var array
     * @static
     */
    private static $isUploading = array();

    /**
     * Decorator entry point.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function decorate(Base\Model $resource)
    {
        $resource->on('beforePopulate', array(__CLASS__, 'init'));
        $resource->on('afterValidate', array(__CLASS__, 'validate'));
        $resource->on('beforeSave', array(__CLASS__, 'format'));
        $resource->on('afterSave', array(__CLASS__, 'upload'));
        $resource->on('afterDelete', array(__CLASS__, 'delete'));
    }

    /**
     * Initialize attachments.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param array      $params   Additional parameters.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function init(Base\Model $resource, array $params)
    {
        self::$attachments = $resource::attachmentsFields();

        foreach (self::$attachments as $name => $_attachment) {
            self::$attachmentsOld[$name] = $resource->{$name};
            self::$isUploading[$name] = Helpers\File::uploadedFileExists($name);
        }
    }

    /**
     * Validates attachment.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param array      $params   Additional parameters.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function validate(Base\Model $resource, array $params)
    {
        foreach (self::$attachments as $name => $_attachment) {
            if (self::$isUploading[$name] && isset($resource->errors[$name])) {
                unset($resource->errors[$name]);
            }

            if (self::$isUploading[$name]) {
                if (!Helpers\File::validate($_FILES[$name], $_attachment['type'], $_attachment['size'])) {
                    $resource->errors[$name] = 'invalid_type';
                }
            }
        }
    }

    /**
     * Formats filename of the attachment.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param array      $params   Additional parameters.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function format(Base\Model $resource, array $params)
    {
        foreach (self::$attachments as $name => $_attachment) {
            if (self::$isUploading[$name]) {
                $resource->{$name} = isset($_attachment['filename']) ?
                    Helpers\File::formatFilename($_attachment['filename'], $_FILES[$name]['name']) :
                    Helpers\File::filterFilename($_FILES[$name]['name']);
            }
        }
    }

    /**
     * Upload attachment to server.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function upload(Base\Model $resource)
    {
        foreach (self::$attachments as $name => $_attachment) {
            $_error_uploading = false;
            $_attachment_name = $resource->{$name};

            if (self::$isUploading[$name]) {
                $successful = Helpers\File::upload(
                    $_FILES[$name],
                    $resource->attachmentsStoragePath($name),
                    $_attachment_name
                );
                if ($successful) {
                    /* Make thumbnails */
                    if ($_attachment['type'] === array('photo') &&
                        isset($_attachment['thumbnails']) &&
                        is_array($_attachment['thumbnails'])
                    ) {
                        self::createThumbnails($resource, $name, $_attachment_name, $_attachment['thumbnails']);
                    }
                } else {
                    $_error_uploading = true;
                }
            }

            /* Delete old attachment and its related files */
            if (!$_error_uploading) {
                $attachment_old_file = Core\Config()->paths('root') .
                    $resource->attachmentsStoragePath($name) .
                    self::$attachmentsOld[$name];

                if ((self::$attachmentsOld[$name] != $_attachment_name) && file_exists($attachment_old_file)) {
                    try {
                        Helpers\File::delete($attachment_old_file);
                    } catch (\Exception $e) {
                    }

                    if ($_attachment['type'] === array('photo') &&
                        isset($_attachment['thumbnails']) &&
                        is_array($_attachment['thumbnails'])
                    ) {
                        self::deleteThumbnails($resource, $name, self::$attachmentsOld[$name]);
                    }
                }
            }
        }
    }

    /**
     * Delete attachment.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function delete(Base\Model $resource)
    {
        self::$attachments = $resource::attachmentsFields();

        foreach (self::$attachments as $name => $_attachment) {
            $attachment_file = Core\Config()->paths('root') .
                $resource->attachmentsStoragePath($name) .
                $resource->{$name};

            if (file_exists($attachment_file)) {
                try {
                    if (file_exists($attachment_file)) {
                        Helpers\File::delete($attachment_file);
                    }
                } catch (\Exception $e) {
                    trigger_error($e->getMessage());
                }

                /* Delete thumbnails */
                if ($_attachment['type'] === array('photo') &&
                    isset($_attachment['thumbnails']) &&
                    is_array($_attachment['thumbnails'])
                ) {
                    self::deleteThumbnails($resource, $name, $resource->{$name});
                }
            }
        }
    }

    /**
     * Create all related file thumbnails.
     *
     * @param Base\Model $resource            Currently processed resource.
     * @param string     $name                Name of the thumbnail.
     * @param string     $attachment_filename Attachment file name.
     * @param array      $thumbnails          Array of thumbnail sizes.
     *
     * @access private
     * @static
     *
     * @return void
     */
    private static function createThumbnails(Base\Model $resource, $name, $attachment_filename, array $thumbnails)
    {
        foreach ($thumbnails as $thumbnail) {
            try {
                switch ($thumbnail['type']) {
                    case 'resize':
                        Helpers\Image::createScaledThumbnail(
                            $resource->attachmentsStoragePath($name) . $attachment_filename,
                            array($thumbnail['size'])
                        );
                        break;

                    case 'crop':
                        Helpers\Image::createCroppedThumbnail(
                            $resource->attachmentsStoragePath($name) . $attachment_filename,
                            array($thumbnail['size'])
                        );
                        break;
                }
            } catch (\Exception $e) {
                var_log($e->getMessage());
            }
        }
    }

    /**
     * Deletes all related file to an attachment.
     *
     * @param Base\Model $resource   Currently processed resource.
     * @param string     $attachment Attachment file name.
     * @param string     $name       Name of the file.
     *
     * @access private
     * @static
     *
     * @return void
     */
    private static function deleteThumbnails(Base\Model $resource, $attachment, $name)
    {
        $_storage_path = $resource->attachmentsStoragePath($attachment);
        foreach (self::$attachments[$attachment]['thumbnails'] as $thumbnail) {
            try {
                if (file_exists($_storage_path . "{$thumbnail['size']}-{$name}")) {
                    Helpers\File::delete($_storage_path . "{$thumbnail['size']}-{$name}");
                }
            } catch (\Exception $e) {
                var_log($e->getMessage());
            }
        }
    }
}
