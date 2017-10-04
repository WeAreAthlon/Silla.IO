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
use Core\Modules\DB;
use Core\Modules\DB\Decorators\Interfaces;

/**
 * Class Attachment Decorator Implementation definition.
 */
abstract class Attachment implements DB\Interfaces\Decorator
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
     * @param Interfaces\Attachment $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function init(Interfaces\Attachment $resource)
    {
        self::$attachments = $resource::attachmentsFields();

        foreach (self::$attachments as $name => $_attachment) {
            self::$attachmentsOld[$name] = $resource->{$name};
            self::$isUploading[$name]    = Helpers\File::uploadedFileExists($name);
        }
    }

    /**
     * Validates attachment.
     *
     * @param \Core\Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function validate(Base\Model $resource)
    {
        foreach (self::$attachments as $name => $_attachment) {
            if (self::$isUploading[$name] && $resource->getError($name)) {
                $resource->removeError($name);
            }

            $file = Core\Router()->request->files($name);

            if (self::$isUploading[$name]) {
                if (!Helpers\File::validate($file, $_attachment['type'], $_attachment['size'])) {
                    $resource->setError($name, 'invalid_type');
                }
            }
        }
    }

    /**
     * Formats filename of the attachment.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function format(Base\Model $resource)
    {
        foreach (self::$attachments as $name => $_attachment) {
            if (self::$isUploading[$name]) {
                $file = Core\Router()->request->files($name);
                $resource->{$name} = isset($_attachment['filename']) ?
                    Helpers\File::formatFilename($_attachment['filename'], $file['name']) :
                    Helpers\File::filterFilename($file['name']);
            }
        }
    }

    /**
     * Upload attachment to server.
     *
     * @param Interfaces\Attachment $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function upload(Interfaces\Attachment $resource)
    {
        foreach (self::$attachments as $name => $_attachment) {
            $_error_uploading = false;
            $_attachment_name = $resource->{$name};
            $file = Core\Router()->request->files($name);

            if (self::$isUploading[$name]) {
                $successful = Helpers\File::upload($file, $resource->attachmentsStoragePath($name), $_attachment_name);
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

                if (self::$attachmentsOld[$name] !== $_attachment_name) {
                    try {
                        Helpers\File::delete($attachment_old_file);
                    } catch (\Exception $e) {
                        var_log($e->getMessage());
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
     * @param Interfaces\Attachment $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function delete(Interfaces\Attachment $resource)
    {
        self::$attachments = $resource::attachmentsFields();

        foreach (self::$attachments as $name => $_attachment) {
            $attachment_file = Core\Config()->paths('root') .
                               $resource->attachmentsStoragePath($name) .
                               $resource->{$name};

            if (file_exists($attachment_file)) {
                try {
                    Helpers\File::delete($attachment_file);
                } catch (\Exception $e) {
                    trigger_error($e->getMessage());
                }

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
     * @param Interfaces\Attachment            $resource   Currently processed resource.
     * @param string                           $name       Name of the thumbnail.
     * @param string                           $filename   Attachment file name.
     * @param array                            $thumbnails Array of thumbnail sizes.
     *
     * @access private
     * @static
     *
     * @return void
     */
    private static function createThumbnails(Interfaces\Attachment $resource, $name, $filename, array $thumbnails)
    {
        foreach ($thumbnails as $thumbnail) {
            try {
                switch ($thumbnail['type']) {
                    case 'scaled':
                        Helpers\Image::createScaledThumbnail(
                            $resource->attachmentsStoragePath($name) . $filename,
                            array($thumbnail['size'])
                        );
                        break;

                    case 'cropped':
                        Helpers\Image::createCroppedThumbnail(
                            $resource->attachmentsStoragePath($name) . $filename,
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
     * @param Interfaces\Attachment $resource   Currently processed resource.
     * @param string                $attachment Attachment file name.
     * @param string                $name       Name of the file.
     *
     * @access private
     * @static
     *
     * @return void
     */
    private static function deleteThumbnails(Interfaces\Attachment $resource, $attachment, $name)
    {
        $filename = $resource->attachmentsStoragePath($attachment) . $name;

        foreach (self::$attachments[$attachment]['thumbnails'] as $thumbnail) {
            try {
                Helpers\File::delete(
                    Core\Helpers\Image::getThumbnailFilePath($filename, $thumbnail['size'], $thumbnail['type'])
                );
            } catch (\Exception $e) {
                var_log($e->getMessage());
            }
        }
    }
}
