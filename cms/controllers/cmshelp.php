<?php
/**
 * CMS Help Controller.
 *
 * @package    Silla.IO
 * @subpackage CMS\Controllers;
 * @author     Rozalia Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Controllers;

use Core;
use Core\Base;
use Core\Modules\Router\Request;
use CMS\Models;
use Parsedown;

/**
 * Class CMS Help Controller definition.
 */
class CMSHelp extends CMS
{
    /**
     * The main model to be used for CRUD.
     *
     * @var string
     */
    protected $model = 'CMS\Models\CMSHelp';

    /**
     * @inheritdoc
     */
    public $skipAclFor = array('preview');

    /**
     * Preview action.
     *
     * @param Request $request Request instance.
     *
     * @return void
     */
    public function preview(Request $request)
    {
        if ($request->is('xhr')) {
            $this->renderer->setLayout(null);
            $this->renderer->setView(null);

            $parser = new Parsedown();
            $this->renderer->setOutput($parser->text($request->post('content')));
        }
    }

    /**
     * @inheritdoc
     */
    protected function loadFormAssets()
    {
        parent::loadFormAssets();

        $this->renderer->assets->add('js/help.js');
    }
}
