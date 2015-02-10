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
     * Create resource - saves model into the database.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @throws \InvalidArgumentException Uploaded media files missing.
     * @uses   Helpers\FlashMessage, Core\Helpers\File, Core\Utils
     *
     * @return void
     */
    public function create(Request $request)
    {
        $this->renderer->setLayout(null);
        $this->limitations = array(
            'upload_file_count' => ini_get('max_file_uploads'),
            'upload_file_size'  => Core\Helpers\File::getMaximumUploadSize(),
        );

        if ($request->is('post')) {
            if ($request->files('media')) {
                $files = $request->files('media');
                $files = Core\Utils::formatArrayOfFiles($files);

                foreach ($files as $file) {
                    #Core\Helpers\File::upload($file, );
                }
            } else {
                throw new \InvalidArgumentException('Uploaded media files missing.');
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
