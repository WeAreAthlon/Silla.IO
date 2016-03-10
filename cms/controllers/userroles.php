<?php
/**
 * UserRoles Controller.
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
 * Class UserRoles Controller definition.
 */
class UserRoles extends CMS
{
    /**
     * The main model to be used for CRUD.
     *
     * @var string
     * @access protected
     */
    protected $model = 'CMS\Models\CMSUserRole';

    /**
     * Permissions scope for the current user session.
     *
     * @var array
     * @access protected
     */
    protected $permissionsScope = array();

    /**
     * Hooks before filters.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('loadPermissionsScope'), array('only' => array('create', 'edit')));
    }

    /**
     * Additional validation rules to ensure user is authorized to edit this resource.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param Request    $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function beforeEdit(Base\Model $resource, Request $request)
    {
        if ($request->is('post')) {
            if (!$request->post('current_password')) {
                $resource->setError('current_password', 'not_empty');
            } else {
                $currentUser = Core\Registry()->get('current_user');

                if (!Crypt::hashCompare($currentUser->password, $request->post('current_password'))) {
                    $resource->setError('current_password', 'mismatch');
                }
            }
        }
    }

    /**
     * Verifies the current user cannot delete his role.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param Request    $request  Current router request.
     *
     * @return void
     */
    protected function beforeDelete(Base\Model $resource, Request $request)
    {
        /* Request current user password before deletion of any User Roles */
        if (!$request->post('password') || !Crypt::hashCompare($this->user->password, $request->post('password'))) {
            if (!$request->is('xhr')) {
                $labelsGeneral = Core\Helpers\YAML::get('general');
                Helpers\FlashMessage::set($labelsGeneral['not_authorized'], 'danger');
            }

            $request->redirectTo('index');
        }

        /* Prevent user role self deletion */
        if ($this->user->role_id == $resource->id) {
            if (!$request->is('xhr')) {
                $labelsErrors = Core\Helpers\YAML::get('messages', $this->labels);
                Helpers\FlashMessage::set($labelsErrors['delete']['self'], 'danger');
            }

            $request->redirectTo('index');
        }

        parent::beforeDelete($resource, $request);
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
