<?php
/**
 * Media Controller.
 *
 * @package    Silla.IO
 * @subpackage CMS\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Controllers;

use Core;
use Core\Base;
use CMS\Helpers;
use Core\Modules\Router\Request;

/**
 * Media class definition.
 */
class Media extends CMS
{
    /**
     * The main model to be used for CRUD.
     *
     * @var string
     */
    protected $model = 'CMS\Models\Medium';

    /**
     * Media limitations container.
     *
     * @var array
     */
    protected $limitations = array();

    /**
     * Create resource - saves model into the database.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @throws \InvalidArgumentException Uploaded media files missing.
     * @uses   Helpers\FlashMessage, Core\Helpers\File, Core\Helpers\YAML, Core\Utils
     *
     * @return void
     */
    public function create(Request $request)
    {
        $isMediaCreated = false;
        $this->renderer->setLayout(null);
        $this->limitations = array(
            'size' => Core\Helpers\File::getMaxUploadSize(),
            'type' => Helpers\Media::getSupportedMediaTypes(),
            'mimeType' => Helpers\Media::getSupportedMimeTypes(),
        );

        if ($request->is('post') && $request->files('media')) {
            $file = $request->files('media');

            if (Core\Helpers\File::validate($file, $this->limitations['type'], $this->limitations['size'])) {
                $fileName = Helpers\Media::generateFileName($file);
                $medium = new $this->model;
                $medium->save(array(
                    'cmsuser_id' => $this->user->id,
                    'size'       => $file['size'],
                    'filename'   => $fileName,
                    'mimetype'   => Core\Helpers\File::getMimeType($file),
                    'title'      => $fileName,
                ));

                if (!$medium->errors) {
                    $storagePath = Core\Config()->getMediaStorageLocation() . Helpers\Media::getSavePath($medium);
                    if (Core\Helpers\File::upload($file, $storagePath, $medium->filename, true)) {
                        $isMediaCreated = true;
                    }
                }
            }

            if (!$isMediaCreated) {

            }
        }
    }

    /**
     * Load vendor assets across the CMS.
     *
     * @return void
     */
    protected function loadVendorAssets()
    {
        parent::loadVendorAssets();

        $this->renderer->assets->add(array('vendor/dropzone/dist/dropzone.js', 'vendor/dropzone/dist/dropzone.css'));
    }
}
