<?php
/**
 * CMS User Roles Controller.
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
use Core\Modules\Crypt\Crypt;
use CMS\Models;
use CMS\Helpers;

/**
 * Class CMS User Roles Controller definition.
 */
class CMSUserRoles extends CMS
{
    /**
     * Resource Model class name.
     *
     * @var string
     */
    protected $resourceModel = 'CMS\Models\CMSUserRole';

    /**
     * Permissions scope for the current user session.
     *
     * @var array
     */
    protected $permissionsScope = array();

    /**
     * UserRoles constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('loadPermissionsScope'), array('only' => array('create', 'edit')));
    }

    /**
     * Additional validation rules to ensure user is authorized to edit this resource.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeEdit(Request $request)
    {
        if ($request->is('post')) {
            if (!$request->post('current_password')) {
                $this->resource->setError('current_password', 'not_empty');
            } else {
                $currentUser = Core\Registry()->get('current_cms_user');

                if (!Crypt::hashCompare($currentUser->password, $request->post('current_password'))) {
                    $this->resource->setError('current_password', 'mismatch');
                }
            }
        }
    }

    /**
     * Verifies the current user cannot delete his role.
     *
     * Request current user password before deletion of any User Roles.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeDelete(Request $request)
    {
        if (!$request->post('password') || !Crypt::hashCompare($this->user->password, $request->post('password'))) {
            if (!$request->is('xhr')) {
                Helpers\FlashMessage::set($this->labels['general']['not_authorized'], 'danger');
            }

            $request->redirectTo('index');
        }

        if ($this->user->role_id == $this->resource->id) {
            if (!$request->is('xhr')) {
                Helpers\FlashMessage::set($this->labels['errors']['delete']['self'], 'danger');
            }

            $request->redirectTo('index');
        }

        parent::beforeDelete($request);
    }

    /**
     * Loads permissions scope.
     *
     * @return void
     */
    protected function loadPermissionsScope()
    {
        $this->permissionsScope = Helpers\CMSUsers::getAccessibilityScope();
    }
}
