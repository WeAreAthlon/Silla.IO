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
     * @uses   Helpers\FlashMessage, Core\Helpers\File, Core\Helpers\YAML, Core\Helpers\Media, Core\Utils
     *
     * @return void
     */
    public function create(Request $request)
    {
        $this->renderer->setLayout(null);
        $this->limitations = array(
            'size' => Core\Helpers\File::getMaxUploadSize(),
            'type' => Core\Helpers\Media::getSupportedMediaTypes(),
            'mimeType' => Core\Helpers\Media::getSupportedMimeTypes(),
        );

        if ($request->is('post') && $request->files('media')) {
            $this->renderer->setView(null);
            $this->renderer->setLayout(null);

            $medium = new $this->model($request->files('media'));
            $medium->save($_POST);

            if ($medium->errors) {
                $message  = array();
                $messages = Core\Helpers\YAML::get('errors');

                foreach($medium->errors as $attribute => $error) {
                    $attribute = ucfirst($attribute);
                    $message[] = "{$attribute}: {$messages[$error]}";
                }

                $this->renderer->setOutput(implode("\n", $message));
                Core\Router()->response->setHttpResponseCode(400);
            }
        }
    }

    /**
     * Retrieve a file
     *
     * @param Request $request Current router request.
     */
    public function assets(Request $request)
    {
        $this->renderer->setLayout(null);
        $this->renderer->setView(null);

        $asset = new $this->model;
        $asset = $asset::find()->where('filename = ?', array($request->get('id')));
        $media = $asset->getThumbnail(160, 100, true);

        if($asset) {
            Core\Router()->response->addHeaders(array(
                'Content-Type:' .$asset->mimetype,
                'Content-Length: ' . filesize($media),
            ));

            $this->renderer->setOutput(file_get_contents($media));
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
