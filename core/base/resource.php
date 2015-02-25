<?php
/**
 * Base Resource Class Definition.
 *
 * Representation of CRUD resource for robust manipulation of data.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;
use Core\Modules\Router\Request;
use Core\Modules\DB;
use CMS\Helpers;
use CMS\Models;

/**
 * Class Resource definition.
 */
abstract class Resource extends Controller
{
    /**
     * Stores the currently managed resource.
     *
     * @var Core\Base\Model
     * @access public
     */
    public $resource = null;

    /**
     * Stores all managed resources.
     *
     * @var mixed
     * @access public
     */
    public $resources = null;

    /**
     * Stores all validation errors.
     *
     * @var array
     * @access public
     */
    public $errors = array();

    /**
     * Default index listing function.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @uses   Helpers\DataTables
     *
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->is('xhr')) {
            $this->renderer->setLayout(null);
            $this->renderer->setView(null);
            $query = Helpers\DataTables::queryModel(new $this->model, $_REQUEST['query']);

            $this->beforeIndex($query, $request);
            $this->resources = $query;

            $response = array(
                'data'       => $this->getPartialOutput('_shared/entities/list/_' . $_REQUEST['view']),
                'pagination' => $this->getPartialOutput('_shared/entities/list/_pagination'),
            );

            $this->renderer->setOutput(json_encode($response));
            Core\Router()->response->addHeader('Content-Type: application/json');
        } else {
            if ($this->getModelName()) {
                $resource = new $this->model;
                $params = $request->get();
                $query = array(
                    'sorting' =>
                        isset($params['sort'], $params['order']) ?
                            array('field' => $params['sort'], 'order' => $params['order']) : null,
                    'filtering' =>
                        isset($params['filtering']) ?
                            $params['filtering'] : null,
                );

                $query = Helpers\DataTables::queryModel($resource, $query);
                $this->beforeIndex($query, $request);

                $this->resources = $query;
            } else {
                $this->beforeIndex(new DB\Query, $request);
            }

            if (!$this->renderer->getView()) {
                $this->renderer->setView('_shared/entities/list/list');
            }
        }

        $this->afterIndex($request);
    }

    /**
     * Show resource.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @uses   Helpers\FlashMessage
     * @uses   Core\Helpers\YAML
     *
     * @return void
     */
    public function show(Request $request)
    {
        $this->renderer->setLayout('modal');

        if (!$request->get('id')) {
            $request->redirectTo('index');
        }

        $resourceModel = new $this->model;
        $resource = $resourceModel::find()
            ->where($resourceModel::primaryKeyField() . ' = ?', array($request->get('id')))->first();

        if (!$resource) {
            $labelsErrors = Core\Helpers\YAML::get('errors');
            Helpers\FlashMessage::setMessage($labelsErrors['not_exists'], 'danger');

            $request->redirectTo('index');
        } else {
            $this->beforeShow($resource, $request);
            $this->resource = $resource;
        }

        if (!$this->renderer->getView()) {
            $this->renderer->setView('_shared/entities/show/show');
        }

        $this->afterShow($resource, $request);
    }

    /**
     * Create resource - saves model into the database.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @uses   Helpers\FlashMessage
     * @uses   Core\Helpers\YAML
     *
     * @return void
     */
    public function create(Request $request)
    {
        $resource = new $this->model;

        $this->beforeCreate($request);

        if ($request->is('post')) {
            $resource->save($request->post());
            $this->afterCreate($resource, $request);
        }

        $this->resource = $resource;

        if (!$this->renderer->getView()) {
            $this->renderer->setView('_shared/entities/form/form');
        }
    }

    /**
     * Modifies resource - updates model into the database.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @uses   Helpers\FlashMessage
     * @uses   Core\Helpers\YAML
     *
     * @return void
     */
    public function edit(Request $request)
    {
        if (!$request->get('id')) {
            $request->redirectTo('index');
        }

        $resourceModel = new $this->model;
        $resource = $resourceModel::find()
            ->where($resourceModel::primaryKeyField() . ' = ?', array($request->get('id')))->first();

        if (!$resource) {
            $labelsErrors = Core\Helpers\YAML::get('errors');
            Helpers\FlashMessage::setMessage($labelsErrors['not_exists'], 'danger');

            $request->redirectTo('index');
        }

        $this->beforeEdit($resource, $request);

        if ($request->is('post')) {
            $resource->save($request->post());
            $this->afterEdit($resource, $request);
        }

        $this->resource = $resource;

        if (!$this->renderer->getView()) {
            $this->renderer->setView('_shared/entities/form/form');
        }
    }

    /**
     * Deletes resource - deletes model from the database.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @uses   Helpers\FlashMessage
     * @uses   Core\Helpers\YAML
     *
     * @return void
     */
    public function delete(Request $request)
    {
        if ($request->is('post') || $request->is('xhr') || $request->is('delete')) {
            if (!$request->get('id')) {
                $request->redirectTo('index');
            }

            $resourceModel = new $this->model;
            $resource = $resourceModel::find()
                ->where($resourceModel::primaryKeyField() . ' = ?', array($request->get('id')))->first();

            if ($resource) {
                $this->beforeDelete($resource, $request);
                $resource->delete();
                $this->afterDelete($resource, $request);
            } else {
                if (!$request->is('xhr')) {
                    $labelsErrors = Core\Helpers\YAML::get('errors');
                    Helpers\FlashMessage::setMessage($labelsErrors['not_exists'], 'danger');
                }

                $request->redirectTo('index');
            }
        } else {
            Core\Router()->response->addHeader($request->type() . ' 403 Forbidden');
        }
    }

    /**
     * Hook - executes before index action.
     *
     * @param DB\Query $query   Current query object.
     * @param Request  $request Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function beforeIndex(DB\Query &$query, Request $request)
    {
    }

    /**
     * Hook - executes after index action.
     *
     * @param Request $request Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function afterIndex(Request $request)
    {
    }

    /**
     * Hook - executes on show action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function beforeShow(Model $resource, Request $request)
    {
    }

    /**
     * Hook - executes on show action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function afterShow(Model $resource, Request $request)
    {
    }

    /**
     * Hook - executes before create action.
     *
     * @param Request $request Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function beforeCreate(Request $request)
    {
    }

    /**
     * Hook - executes after create action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function afterCreate(Model $resource, Request $request)
    {
        if (!empty($resource->errors)) {
            $labelsErrors = Core\Helpers\YAML::get('errors');
            Helpers\FlashMessage::setMessage($labelsErrors['general'], 'danger', $resource->errors);
        } else {
            $labelsMessages = Core\Helpers\YAML::get('messages', $this->labels);
            Helpers\FlashMessage::setMessage($labelsMessages['create']['success'], 'success');

            $request->redirectTo('index');
        }
    }

    /**
     * Hook - executes before edit action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function beforeEdit(Model $resource, Request $request)
    {
    }

    /**
     * Hook - executes after edit action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function afterEdit(Model $resource, Request $request)
    {
        if (!empty($resource->errors)) {
            $labelsErrors = Core\Helpers\YAML::get('errors');
            Helpers\FlashMessage::setMessage($labelsErrors['general'], 'danger', $resource->errors);
        } else {
            $labelsMessages = Core\Helpers\YAML::get('messages', $this->labels);
            Helpers\FlashMessage::setMessage($labelsMessages['edit']['success'], 'success');
        }
    }

    /**
     * Hook - executes before delete action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function beforeDelete(Model $resource, Request $request)
    {
    }

    /**
     * Hook - executes after delete action.
     *
     * @param Model   $resource Currently processed resource.
     * @param Request $request  Current router request.
     *
     * @access protected
     *
     * @return void
     */
    protected function afterDelete(Model $resource, Request $request)
    {
        if (!$request->is('xhr')) {
            $labelsMessages = Core\Helpers\YAML::get('messages', $this->labels);
            Helpers\FlashMessage::setMessage($labelsMessages['delete']['success'], 'warning');
        }

        $request->redirectTo('index');
    }

    /**
     * Exports data from a database model.
     *
     * @param Request $request Current router request.
     *
     * @access public
     * @uses   CMS\Helpers\Export
     * @uses   CMS\Helpers\CMSUsers
     * @uses   Core\Helpers\YAML
     *
     * @return void
     */
    public function export(Request $request)
    {
        $this->renderer->setLayout(null);
        $this->renderer->setView(null);

        if ($request->get('type')) {
            $fieldsToExport = array();
            $attributes = array();

            /* Determine which model data fields to export */
            $sections = Core\Helpers\YAML::get('attributes', $this->labels);

            foreach ($sections as $section) {
                $attributes = array_merge($attributes, $section['fields']);
            }

            foreach ($attributes as $key => $attr) {
                $attr['export'] = isset($attr['export']) ? $attr['export'] : true;

                if ($attr['export']) {
                    $fieldsToExport[$key] = $attr['title'];
                }
            }

            $model = new $this->model;
            $query = Helpers\DataTables::toQuery($model, array_keys($fieldsToExport), $request->get());
            $exportFile = Core\Config()->paths('tmp') . md5($this->model) . '.export.tmp';

            if (Helpers\Export::populateCsvfileCustomQuery(
                array('fields' => $fieldsToExport, 'query' => $query),
                $exportFile
            )) {
                if ('pdf' === $request->get('type')) {
                    $cmsLabels = Core\Helpers\YAML::getAll('globals');

                    $title  = $cmsLabels['export']['caption'] . ' ' .
                        $cmsLabels['modules'][$this->getControllerName()]['title'];
                    $assets = Core\Config()->paths('assets');
                    $logo   = $assets['distribution'] . 'img' . DIRECTORY_SEPARATOR . 'logo.png';
                    $pdf    = new Helpers\PDF($title, 'freeserif', $logo);

                    $pdf->SetAuthor($cmsLabels['client'], true);
                    $pdf->AddPage();
                    $pdf->embedContentTable(array_values($fieldsToExport), $pdf->LoadData($exportFile));
                    $pdf->Output();
                } else {
                    Helpers\Export::getCsvBuffered($exportFile);
                }
            }
        } else {
            $request->redirectTo('index');
        }
    }
}
