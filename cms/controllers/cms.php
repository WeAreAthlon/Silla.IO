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
use Core\Modules\DB;
use Core\Modules\Router\Request;
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

        parent::__construct();
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
            'vendor/components/jquery/jquery.js',
            'vendor/components/bootstrap/js/bootstrap.js',
            'vendor/components/bootstrap/css/bootstrap.css',
            'vendor/pnikolov/bootstrap-chosen/js/chosen.jquery.js',
            'vendor/pnikolov/bootstrap-chosen/css/chosen.css',
            'vendor/drmonty/ekko-lightbox/js/ekko-lightbox.js',
            'vendor/drmonty/ekko-lightbox/css/ekko-lightbox.css',
            'vendor/pnikolov/bootbox/js/bootbox.js',
            'vendor/pnikolov/spin.js/js/spin.js',
            'cms/assets/css/bootstrap-theme.silla.css',
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
            'cms/assets/js/cms.silla.js',
            'cms/assets/js/init.silla.js',
            'cms/assets/css/default.silla.css',
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
            'vendor/moment/moment/min/moment.min.js',
            'vendor/eonasdan/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
            'vendor/eonasdan/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
            'vendor/pnikolov/bootstrap-maxlength/js/bootstrap-maxlength.js',
            'vendor/pnikolov/bootstrap-daterangepicker/js/daterangepicker.js',
            'vendor/pnikolov/bootstrap-daterangepicker/css/daterangepicker.css',
            'vendor/itsjaviaguilar/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js',
            'vendor/itsjaviaguilar/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css',
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
            'vendor/moment/moment/min/moment.min.js',
            'vendor/pnikolov/bootstrap-daterangepicker/js/daterangepicker.js',
            'vendor/pnikolov/bootstrap-daterangepicker/css/daterangepicker.css',
            'vendor/eonasdan/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
            'vendor/eonasdan/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
            'vendor/pnikolov/jquery-serialize-object/js/jquery.serialize-object.js',
            'cms/assets/js/libs/obj.silla.js',
            'cms/assets/js/libs/datatables.silla.js',
        ));
    }

    /**
     * Filter resources to only owned resources.
     *
     * @param \Core\Modules\DB\Query       $query   Initial resource query.
     * @param \Core\Modules\Router\Request $request Current router request.
     *
     * @return void
     */
    protected function beforeIndex(DB\Query &$query, Request $request)
    {
        parent::beforeIndex($query, $request);

        if ($this->user->hasOwnershipOver($this->resourceModel)) {
            $query = Helpers\CMSUsers::filterOwnResources($this->resourceModel);
        }
    }

    /**
     * Prevent association of not "owned" resources.
     *
     * @param \Core\Modules\Router\Request $request Current router request.
     *
     * @return void
     */
    protected function beforeCreate(Request $request)
    {
        parent::beforeCreate($request);

        if ($request->is('post')) {
            $this->preventAssociationOfNotOwnedResource($request);
        }
    }

    /**
     * Assign "ownership" to the created resource.
     *
     * @param \Core\Modules\Router\Request $request Current router request.
     *
     * @return void
     */
    protected function afterCreate(Request $request)
    {
        if (!$this->resource->hasErrors()) {
            Helpers\CMSUsers::assignResource($this->resource);
        }

        parent::afterCreate($request);
    }

    /**
     * Prevent association of not "owned" resources.
     *
     * @param \Core\Modules\Router\Request $request Current router request.
     *
     * @return void
     */
    protected function beforeEdit(Request $request)
    {
        parent::beforeEdit($request);

        if ($request->is('post')) {
            $this->preventAssociationOfNotOwnedResource($request);
        }
    }

    /**
     * Retract "ownership" to the deleted resource.
     *
     * @param \Core\Modules\Router\Request $request Current router request.
     *
     * @return void
     */
    protected function afterDelete(Request $request)
    {
        Helpers\CMSUsers::retractResource($this->resource);

        parent::afterDelete($request);
    }

    /**
     * Ensure that the current requested resource is within the ownership scope.
     *
     * @param \Core\Modules\Router\Request $request Current Router Request.
     *
     * @return void
     */
    protected function loadResource(Request $request)
    {
        parent::loadResource($request);

        $resourceModel = $this->resourceModel;

        if ($this->user->hasOwnershipOver($resourceModel) && !Helpers\CMSUsers::userOwnsResource($this->resource)) {
            Helpers\FlashMessage::set($this->labels['errors']['not_exists'], 'danger');

            $request->redirectTo('index', 404);
        }
    }

    /**
     * Prevents Association of not owned resource.
     *
     * @param \Core\Modules\Router\Request $request Request object.
     *
     * @return void
     */
    private function preventAssociationOfNotOwnedResource(Request $request)
    {
        foreach ($this->attributes as $attribute => $options) {
            if ($request->post($attribute)) {
                $association = $this->resource->getAssociationMetaDataByKey($attribute);

                if (!$association && isset($this->resource->hasAndBelongsToMany[$attribute])) {
                    $association = $this->resource->hasAndBelongsToMany[$attribute];
                }

                if ($association && $this->user->hasOwnershipOver($association['class_name'])) {
                    if (!Helpers\CMSUsers::userOwns($request->post($attribute), $association['class_name'])) {
                        $this->resource->setError($attribute, 'not_exists');
                    }
                }
            }
        }
    }
}
