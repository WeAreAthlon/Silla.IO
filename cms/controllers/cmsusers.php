<?php
/**
 * CMS Users Controller.
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
 * Class CMS Users Controller definition.
 */
class CMSUsers extends CMS
{
    /**
     * Resource Model class name.
     *
     * @var string
     */
    protected $resourceModel = 'CMS\Models\CMSUser';

    /**
     * Additional validation rules to ensure password confirmation.
     *
     * @inheritdoc
     */
    protected function beforeCreate(Request $request)
    {
        parent::beforeCreate($request);

        /* Remove current password validation. */
        unset(
            $this->sections['general']['fields']['current_password'],
            $this->sections['credentials']['fields']['current_password']
        );

        if ($request->is('post')) {
            if ($request->post('password') !== $request->post('password_confirm')) {
                $this->resource->setError('password_confirm', 'mismatch');
            }
        }
    }


    /**
     * Additional validation rules to ensure user is authorized to edit this resource.
     *
     * @inheritdoc
     */
    protected function beforeEdit(Request $request)
    {
        parent::beforeEdit($request);

        if ($request->is('post')) {
            if (!Crypt::hashCompare($this->user->password, $request->post('current_password'))) {
                $this->resource->setError('current_password', 'mismatch');
            }

            if ($request->post('password') !== $request->post('password_confirm')) {
                $this->resource->setError('password_confirm', 'mismatch');
            }
        }
    }

    /**
     * Change access credentials action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function credentials(Request $request)
    {
        $this->loadResource($request);

        /* Prevent access to other resource attributes except credentials. */
        $this->removeAccessibleAttributes(array_merge(
            array_keys($this->sections['general']['fields']),
            array_keys($this->sections['settings']['fields'])
        ));

        unset($this->sections['general'], $this->sections['settings']);

        $this->edit($request);

        if ($request->is('post') && !$this->resource->hasErrors()) {
            $request->redirectTo('index');
        }
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
                Helpers\FlashMessage::set($this->labels['general']['not_authorized'], 'danger');
            }

            $request->redirectTo('index');
        }

        if ($this->resource->id == $this->user->id) {
            if (!$request->is('xhr')) {
                Helpers\FlashMessage::set($this->labels['messages']['delete']['self'], 'danger');
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

        if ($request->is('post') && !$this->resource->hasErrors() && ($this->resource->id == $this->user->id)) {
            Core\Session()->set('cms_user_info', rawurlencode(serialize($this->resource)));
            $this->user = $this->resource;
        }
    }

    /**
     * Prevent credentials management on edit action.
     *
     * @inheritdoc
     */
    protected function loadAttributeSections(Request $request)
    {
        parent::loadAttributeSections($request);

        if($request->action() === 'edit') {
            $this->removeAccessibleAttributes(array_keys($this->sections['credentials']['fields']));
            unset($this->sections['credentials']);
        }
    }
}
