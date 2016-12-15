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

use Core\Modules\Router\Request;
use Core\Modules\Crypt\Crypt;
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
    public $resourceModel = 'CMS\Models\CMSUserRole';

    /**
     * Access scope for the current user session.
     *
     * @var array
     */
    protected $scope = array();

    /**
     * Remove current password validation.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeCreate(Request $request)
    {
        unset($this->sections['general']['fields']['current_password']);
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
            if (!Crypt::hashCompare($this->user->password, $request->post('current_password'))) {
                $this->resource->setError('current_password', 'mismatch');
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

        if ($this->user->role_id == $this->resource->getPrimaryKeyValue()) {
            if (!$request->is('xhr')) {
                Helpers\FlashMessage::set($this->labels['errors']['delete']['self'], 'danger');
            }

            $request->redirectTo('index');
        }

        parent::beforeDelete($request);
    }

    /**
     * Loads access scope.
     *
     * @return void
     */
    protected function loadAccessibilityScope()
    {
        parent::loadAccessibilityScope();

        $this->scope = array(
            'permissions' => Helpers\CMSUsers::getAccessibilityScope(),
            'ownership' => Helpers\Ownership::getScope($this->labels['modules']),
        );
    }
}
