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

/**
 * Class Resource definition.
 */
abstract class Resource extends Controller
{
    /**
     * Stores the currently managed resource.
     *
     * @var Core\Base\Model
     */
    public $resource = null;

    /**
     * Stores all managed resources.
     *
     * @var mixed
     */
    public $resources = null;

    /**
     * Stores all validation errors.
     *
     * @var array
     */
    public $errors = array();

    /**
     * Resource accessible attributes.
     *
     * @var array
     */
    protected $accessibleAttributes = array();

    /**
     * Resource Model Name.
     *
     * @var string
     */
    public $resourceModel = '';

    /**
     * Resource Sections.
     *
     * @var array
     */
    public $sections = array();

    /**
     * Resource Attributes.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Resource constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->resourceModel) {
            $this->resource = new $this->resourceModel;
            $this->accessibleAttributes = array_merge(
                array_keys($this->resource->fields()),
                array_keys($this->resource->hasMany),
                array_keys($this->resource->hasAndBelongsToMany)
            );

            $this->addBeforeFilters(array('loadAttributeSections'), array('except' => 'delete'));
            $this->addBeforeFilters(array('loadResource'), array('only' => array('show', 'edit', 'delete')));
            $this->addAfterFilters(array('loadFlashMessage'));
        }
    }

    /**
     * Default index listing function.
     *
     * @param Request $request Current router request.
     *
     * @uses   Helpers\DataTables
     *
     * @return void
     */
    public function index(Request $request)
    {
        $resourceModel = $this->resourceModel;
        $query = $resourceModel::find();

        if ($request->is('xhr')) {
            $this->renderer->setLayout(null);
            $this->renderer->setView(null);

            $this->beforeIndex($query, $request);
            $query = Helpers\DataTables::formatQuery($query, $request->variables('query'));

            $this->resources = $query;

            $response = array(
                'data'       => $this->getPartialOutput('_shared/entities/list/_' . $request->variables('view')),
                'pagination' => $this->getPartialOutput('_shared/entities/list/_pagination'),
            );

            $this->renderer->setOutput(json_encode($response));
            $this->renderer->setOutputContentType('application/json');
        } else {
            if ($this->resource) {
                $params = $request->get();
                $queryParams = array(
                    'sorting' =>
                        isset($params['sort'], $params['order']) ?
                            array('field' => $params['sort'], 'order' => $params['order']) : null,
                    'filtering' =>
                        isset($params['filtering']) ?
                            $params['filtering'] : null,
                );

                $this->beforeIndex($query, $request);
                $query = Helpers\DataTables::formatQuery($query, $queryParams);
                $this->resources = $query;
            } else {
                $this->beforeIndex($query, $request);
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
     * @return void
     */
    public function show(Request $request)
    {
        $this->renderer->setLayout('modal');
        $this->beforeShow($request);

        if (!$this->renderer->getView()) {
            $this->renderer->setView('_shared/entities/show/show');
        }

        $this->afterShow($request);
    }

    /**
     * Create resource - saves model into the database.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function create(Request $request)
    {
        $this->beforeCreate($request);

        if ($request->is('post')) {
            $this->resource->save($this->filterAccessibleAttributesData($request->post()));
            $this->afterCreate($request);
        }

        if (!$this->renderer->getView()) {
            $this->renderer->setView('_shared/entities/form/form');
        }
    }

    /**
     * Modifies resource - updates model into the database.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function edit(Request $request)
    {
        $this->beforeEdit($request);

        if ($request->is('post')) {
            $this->resource->save($this->filterAccessibleAttributesData($request->post()));
            $this->afterEdit($request);
        }

        if (!$this->renderer->getView()) {
            $this->renderer->setView('_shared/entities/form/form');
        }
    }

    /**
     * Deletes resource - deletes model from the database.
     *
     * @param Request $request Current router request.
     *
     * @uses   Helpers\FlashMessage
     * @uses   Core\Helpers\YAML
     *
     * @return void
     */
    public function delete(Request $request)
    {
        if ($request->is('post') || $request->is('xhr') || $request->is('delete')) {
            if ($this->resource->exists()) {
                $this->beforeDelete($request);
                $this->resource->delete();
                $this->afterDelete($request);
            } else {
                if (!$request->is('xhr')) {
                    Helpers\FlashMessage::set($this->labels['errors']['not_exists'], 'danger');
                }

                $request->redirectTo('index');
            }
        } else {
            Core\Router()->response->setHttpResponseCode(403);
        }
    }

    /**
     * Hook - executes before index query.
     *
     * @param DB\Query $query   Current query object.
     * @param Request  $request Current router request.
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
     * @return void
     */
    protected function afterIndex(Request $request)
    {
    }

    /**
     * Hook - executes on show action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeShow(Request $request)
    {
    }

    /**
     * Hook - executes on show action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function afterShow(Request $request)
    {
    }

    /**
     * Hook - executes before create action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeCreate(Request $request)
    {
    }

    /**
     * Add access to a resource attribute.
     *
     * @param array $fields Attribute field names.
     *
     * @return void
     */
    protected function addAccessibleAttributes(array $fields)
    {
        $this->accessibleAttributes = array_unique(array_merge($this->accessibleAttributes, $fields));
    }

    /**
     * Remove access to a resource attribute.
     *
     * @param array $fields Attribute field names.
     *
     * @return void
     */
    protected function removeAccessibleAttributes(array $fields)
    {
        $this->accessibleAttributes = array_filter($this->accessibleAttributes, function ($attribute) use ($fields) {
            return !in_array($attribute, $fields, true);
        });
    }

    /**
     * Filter resource attributes per access scope.
     *
     * @param array $data Input data.
     *
     * @return array
     */
    protected function filterAccessibleAttributesData(array $data)
    {
        return array_intersect_key($data, array_flip($this->accessibleAttributes));
    }

    /**
     * Hook - executes after create action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function afterCreate(Request $request)
    {
        if ($this->resource->hasErrors()) {
            Helpers\FlashMessage::set($this->labels['errors']['general'], 'danger', $this->resource->errors());
        } else {
            Helpers\FlashMessage::set($this->labels['messages']['create']['success'], 'success');

            $request->redirectTo('index');
        }
    }

    /**
     * Hook - executes before edit action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeEdit(Request $request)
    {
    }

    /**
     * Hook - executes after edit action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function afterEdit(Request $request)
    {
        if ($this->resource->hasErrors()) {
            Helpers\FlashMessage::set($this->labels['errors']['general'], 'danger', $this->resource->errors());
        } else {
            Helpers\FlashMessage::set($this->labels['messages']['edit']['success'], 'success');
        }
    }

    /**
     * Hook - executes before delete action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function beforeDelete(Request $request)
    {
    }

    /**
     * Hook - executes after delete action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function afterDelete(Request $request)
    {
        if (!$request->is('xhr')) {
            Helpers\FlashMessage::set($this->labels['messages']['delete']['success'], 'warning');
        }

        $request->redirectTo('index');
    }

    /**
     * Exports data from a database model.
     *
     * @param Request $request Current router request.
     *
     * @uses   CMS\Helpers\Export
     * @uses   CMS\Helpers\CMSUsers
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

            /* Determine which model data fields to export. */
            $sections = $this->labels['attributes'];

            foreach ($sections as $section) {
                $attributes = array_merge($attributes, $section['fields']);
            }

            foreach ($attributes as $key => $attr) {
                $attr['export'] = isset($attr['export']) ? $attr['export'] : true;

                if ($attr['export']) {
                    $fieldsToExport[$key] = $attr['title'];
                }
            }

            $query = Helpers\DataTables::toQuery($this->resource, array_keys($fieldsToExport), $request->get());
            $exportFile = Core\Config()->paths('tmp') . md5($this->resourceModel) . '.export.tmp';

            if (Helpers\Export::populateCsvfileCustomQuery(
                array('fields' => $fieldsToExport, 'query' => $query),
                $exportFile
            )) {
                if ('pdf' === $request->get('type')) {
                    $cmsLabels = $this->labels;

                    $title  = $cmsLabels['export']['caption'] . ' ' .
                        $cmsLabels['modules'][$this->getControllerName()]['title'];
                    $assets = Core\Config()->paths('assets');
                    $logo   = $assets['distribution'] . 'img' . DIRECTORY_SEPARATOR . 'logo.png';
                    $pdf    = new Helpers\PDF($title, 'freeserif', $logo);

                    $pdf->SetAuthor($cmsLabels['client']);
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

    /**
     * Loads Resource object.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function loadResource(Request $request)
    {
        if (!$this->resource->exists()) {
            if (!$request->get('id')) {
                $request->redirectTo('index');
            }

            $resourceModel = $this->resource;
            $this->resource = $resourceModel::find()
                ->where($resourceModel::primaryKeyField() . ' = ?', array($request->get('id')))->first();

            if (!$this->resource) {
                Helpers\FlashMessage::set($this->labels['errors']['not_exists'], 'danger');

                $request->redirectTo('index');
            }
        }
    }

    /**
     * Loads Flash Messages.
     *
     * @return void
     */
    protected function loadFlashMessage()
    {
        $this->renderer->set('flash', Helpers\FlashMessage::get());
    }

    /**
     * Loads Attribute Sections.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    protected function loadAttributeSections(Request $request)
    {
        $this->sections = $this->labels['attributes'];

        foreach ($this->sections as $section) {
            if (isset($section['fields']) && $section['fields'] && is_array($section['fields'])) {
                $this->attributes = array_merge($this->attributes, $section['fields']);
            }
        }
    }
}
