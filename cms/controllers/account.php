<?php
/**
 * Account Controller.
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
use Core\Modules\Router\Request;
use CMS\Models;
use CMS\Helpers;

/**
 * Class Account Controller definition.
 */
class Account extends CMSUsers
{
    /**
     * Labels names.
     *
     * @var array
     */
    public $labels = array('cmsusers', 'account');

    /**
     * Skips ACL generations for the listed methods.
     *
     * @var array
     */
    public $skipAclFor = array('index', 'create', 'delete', 'show', 'export');

    /**
     * Account constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('assignCurrentUserAsResource'));
    }

    /**
     * Account action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $this->edit($request);
    }

    /**
     * Assign current user as a resource object.
     *
     * @param Request $request Current Router Request.
     *
     * @return void
     */
    protected function assignCurrentUserAsResource(Request $request)
    {
        if (in_array($request->action(), array('edit', 'delete', 'show', 'export'), true)) {
            $request->redirectTo('index');
        }

        $this->resource = $this->user;
        $this->removeAccessibleAttributes(array('role_id'));
    }

    /**
     * Prevent credentials management on edit action.
     *
     * @inheritdoc
     */
    protected function loadAttributeSections(Request $request)
    {
        parent::loadAttributeSections($request);

        if ($request->action() === 'index') {
            $this->removeAccessibleAttributes(array_keys($this->sections['credentials']['fields']));
            unset($this->sections['credentials']);
        }
    }
}
