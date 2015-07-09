<?php
/**
 * Help Controller.
 *
 * @package    Silla.IO
 * @subpackage CMS\Controllers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Controllers;

use Core;
use Core\Base;
use Core\Base\Model;
use Core\Modules\Router\Request;
use CMS\Models;
use Parsedown;

/**
 * Class Help Controller definition.
 */
class Help extends CMS
{
    /**
     * The main model to be used for CRUD.
     *
     * @var string
     */
    protected $model = 'CMS\Models\CMSHelp';

    /**
     * Init method.
     */
    public function __construct()
    {
        parent::__construct();

        $this->skipAclFor(array('create', 'delete', 'export', 'show'));
    }

    /**
     * Load CMS help asset in before edit action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @return void
     */
    protected function beforeEdit(Model $resource, Request $request)
    {
        $this->renderer->assets->add(array(
            'js/help.js',
        ));

        parent::beforeEdit($resource, $request);
    }

    /**
     * Index.
     *
     * @param Request $request Request instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $resource   = $this->model;
        $resource   = $resource::find()->where('name = "help"')->first();
        $parsedown  = new Parsedown();
        $this->content = $parsedown->text($resource->content);
    }

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

            $parsedown = new Parsedown();
            $this->renderer->setOutput($parsedown->text($request->post('content')));
        }
    }
}
