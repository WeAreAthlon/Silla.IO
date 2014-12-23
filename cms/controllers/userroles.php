<?php
/**
 * UserRoles Controller.
 *
 * @package    Silla
 * @subpackage CMS\Controllers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
     * Hooks before fitlers.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('loadPermissionsScope'), array('only' => array('create', 'edit')));
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
                Helpers\FlashMessage::setMessage($labelsGeneral['not_authorized'], 'danger');
            }

            $request->redirectTo('index');
        }

        /* Prevent user role self deletion */
        if ($this->user->role_id === $resource->id) {
            if (!$request->is('xhr')) {
                $labelsErrors = Core\Helpers\YAML::get('messages', $this->labels);
                Helpers\FlashMessage::setMessage($labelsErrors['delete']['self'], 'danger');
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
