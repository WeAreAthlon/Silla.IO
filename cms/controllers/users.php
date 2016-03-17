<?php
/**
 * Users Controller.
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
 * Class Users Controller definition.
 */
class Users extends CMS
{
    /**
     * Resource Model class name.
     *
     * @var string
     */
    protected $resourceModel = 'CMS\Models\CMSUser';

    /**
     * Additional validation rules to ensure user is authorized to edit this resource.
     *
     * @inheritdoc
     */
    protected function beforeEdit(Request $request)
    {
        if ($request->is('post')) {
            if (!Crypt::hashCompare($this->user->password, $request->post('current_password'))) {
               // $this->resource->setError('current_password', 'mismatch');
            }

            if ($request->post('password') !== $request->post('password_confirm')) {
                $this->resource->setError('password_confirm', 'mismatch');
            }
        }
    }

    /**
     * Account action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function account(Request $request)
    {
        $this->resource = $this->user;
        $this->removeAccessibleAttribute('role_id');

        $this->edit($request);
    }

    /**
     * Verifies that the current logged user cannot delete himself.
     *
     * Request current user password before deletion of any users.
     *
     * @inheritdoc
     */
    protected function beforeDelete(Request $request)
    {
        if (!$request->post('password') || !Crypt::hashCompare($this->user->password, $request->post('password'))) {
            if (!$request->is('xhr')) {
                $labelsGeneral = Core\Helpers\YAML::get('general');
                Helpers\FlashMessage::set($labelsGeneral['not_authorized'], 'danger');
            }

            $request->redirectTo('index');
        }

        if ($this->resource->id == $this->user->id) {
            if (!$request->is('xhr')) {
                $labelsMessages = Core\Helpers\YAML::get('messages', $this->labels);
                Helpers\FlashMessage::set($labelsMessages['delete']['self'], 'danger');
            }

            $request->redirectTo('index');
        }

        parent::beforeDelete($request);
    }

    /**
     * Reloads current user info stored in the application session.
     *
     * @inheritdoc
     */
    protected function afterEdit(Request $request)
    {
        parent::afterEdit($request);

        if ($request->is('post') && !$this->resource->hasErrors() && $this->resource->id == $this->user->id) {
            Core\Session()->set('user_info', rawurlencode(serialize($this->resource)));
        }
    }
}
