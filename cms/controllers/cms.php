<?php
/**
 * CMS Controller.
 *
 * @package    Silla.IO
 * @subpackage CMS\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Controllers;

use Core;
use Core\Modules\Router\Request;
use CMS\Models;
use CMS\Helpers;

/**
 * Class CMS Controller definition.
 */
abstract class CMS extends Core\Base\Resource
{
    /**
     * Skips ACL generations for the listed methods.
     *
     * @var array
     */
    public $skipAclFor = array();

    /**
     * Loaded CMS modules.
     *
     * @var array
     */
    public $modules = array();

    /**
     * Currently logged user instance.
     *
     * @var \CMS\Models\CMSUser
     */
    protected $user;

    /**
     * CMS constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addBeforeFilters(array('checkLogged'));
        $this->addBeforeFilters(array('checkPermissions'), array('except' => $this->skipAclFor));
        $this->addBeforeFilters(array('loadVendorAssets'));

        $this->addBeforeFilters(array('loadFormAssets'), array(
            'only' => array('create', 'edit')
        ));

        $this->addBeforeFilters(array('loadListingAssets'), array(
            'only' => array('index')
        ));

        $this->addAfterFilters(array('loadAccessibilityScope', 'loadCmsAssets'));
    }

    /**
     * Login verification gate.
     *
     * @param Request $request Current Router request.
     *
     * @return void
     */
    protected function checkLogged(Request $request)
    {
        if (1 === Core\Session()->get('cms_user_logged')) {
            $this->user = unserialize(rawurldecode(Core\Session()->get('cms_user_info')));

            Core\Registry()->set('current_cms_user', $this->user);
            Core\Helpers\DateTime::setEnvironmentTimezone($this->user->timezone);
        } else {
            $request->redirectTo(array(
                'controller' => 'authentication',
                'action'     => 'login',
                'redirect'   => $request->meta('REQUEST_URI'),
            ));
        }
    }

    /**
     * Permissions verification gate.
     *
     * @param Request $request Current Router Request.
     *
     * @see    CMS\Helpers\CMSUsers::userCan()
     *
     * @return boolean
     */
    protected function checkPermissions(Request $request)
    {
        $controller = $this->getControllerName();
        $action     = $this->getActionName();

        if (!Helpers\CMSUsers::userCan(array('controller' => $controller, 'action' => $action))) {
            Helpers\FlashMessage::set($this->labels['general']['no_access'], 'danger');

            $request->redirectTo(array('controller' => 'account'));
        }

        return true;
    }

    /**
     * Loads CMS accessibility scope.
     *
     * @see    CMS\Helpers\CMSUsers::userCan()
     *
     * @return void
     */
    protected function loadAccessibilityScope()
    {
        $this->modules = array_keys($this->labels['modules']);

        foreach ($this->modules as $key => $module) {
            if (!Helpers\CMSUsers::userCan(array('controller' => $module, 'action' => 'index'))) {
                unset($this->modules[$key]);
            }
        }
    }

    /**
     * Load vendor assets across the CMS.
     *
     * @return void
     */
    protected function loadVendorAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/jquery/dist/jquery.min.js',
            'vendor/bootstrap/dist/js/bootstrap.min.js',
            'vendor/bootstrap/dist/css/bootstrap.css',
            'vendor/pnikolov-bootstrap-chosen/js/chosen.jquery.min.js',
            'vendor/pnikolov-bootstrap-chosen/css/chosen.min.css',
            'vendor/ekko-lightbox/dist/ekko-lightbox.min.js',
            'vendor/ekko-lightbox/dist/ekko-lightbox.min.css',
            'vendor/bootbox/bootbox.js',
            'vendor/spin.js/spin.js',
            'css/bootstrap-theme.athlon.css',
        ));
    }

    /**
     * Load CMS mode assets.
     *
     * @return void
     */
    protected function loadCmsAssets()
    {
        $this->renderer->assets->add(array(
            'js/cms.js',
            'js/init.js',
            'css/style.css',
        ));
    }

    /**
     * Loads form builder generator assets.
     *
     * @access protected
     *
     * @return void
     */
    protected function loadFormAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/moment/min/moment.min.js',
            'vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            'vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'vendor/bootstrap-maxlength/bootstrap-maxlength.min.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker-bs3.css',
            'vendor/pnikolov-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
            'vendor/pnikolov-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
        ));
    }

    /**
     * Loads data table listing assets.
     *
     * @access protected
     *
     * @return void
     */
    protected function loadListingAssets()
    {
        $this->renderer->assets->add(array(
            'vendor/moment/min/moment.min.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker.js',
            'vendor/pnikolov-bootstrap-daterangepicker/daterangepicker-bs3.css',
            'vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            'vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'vendor/jquery-serialize-object/dist/jquery.serialize-object.min.js',
            'js/libs/obj.js',
            'js/libs/datatables.js',
        ));
    }
}
