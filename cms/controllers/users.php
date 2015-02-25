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
     * The main model to be used for CRUD.
     *
     * @var string
     */
    protected $model = 'CMS\Models\CMSUser';

    /**
     * Account action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function account(Request $request)
    {
        $user = $this->user;
        parent::beforeEdit($user, $request);

        if ($request->is('post')) {
            unset($_POST['role_id']);
            $data = $request->post();

            if ($user->save($data)) {
                /* Reloads user info */
                $resource = new $this->model;
                $resource = $resource::find()->where('id = ?', array($user->id))->first();
                Core\Session()->set('user_info', rawurlencode(serialize($resource)));
            }

            parent::afterEdit($user, $request);
        }

        $this->resource = $user;
        $this->renderer->setView('_shared/entities/form/form');
    }

    /**
     * Verifies that the current logged user cannot delete himself.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param Request    $request  Current router request.
     *
     * @return void
     */
    protected function beforeDelete(Base\Model $resource, Request $request)
    {
        /* Request current user password before deletion of any users */
        if (!$request->post('password') || !Crypt::hashCompare($this->user->password, $request->post('password'))) {
            if (!$request->is('xhr')) {
                $labelsGeneral = Core\Helpers\YAML::get('general');
                Helpers\FlashMessage::setMessage($labelsGeneral['not_authorized'], 'danger');
            }

            $request->redirectTo('index');
        }

        /* Prevent self deletion */
        if ($resource->id === $this->user->id) {
            if (!$request->is('xhr')) {
                $labelsMessages = Core\Helpers\YAML::get('messages', $this->labels);
                Helpers\FlashMessage::setMessage($labelsMessages['delete']['self'], 'danger');
            }

            $request->redirectTo('index');
        }

        parent::beforeDelete($resource, $request);
    }

    /**
     * Reloads current user info stored in the app session.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param Request    $request  Current router request.
     *
     * @return void
     */
    protected function afterEdit(Base\Model $resource, Request $request)
    {
        /* Reloads user info */
        if ($resource->id === $this->user->id) {
            $resource = new $this->model;
            $resource = $resource::find()->where('id = ?', array($this->user->id))->first();
            Core\Session()->set('user_info', rawurlencode(serialize($resource)));
        }

        parent::afterEdit($resource, $request);
    }
}
