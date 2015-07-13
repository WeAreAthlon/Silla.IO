<?php
/**
 * Controller implementation.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;
use Core\Modules;

/**
 * Class Controller definition.
 */
abstract class Controller
{
    /**
     * Render instance.
     *
     * @var Core\Modules\Render\Render
     * @access public
     */
    public $renderer;

    /**
     * Renderer adapter name.
     *
     * @var string
     * @access public
     */
    public $rendererAdapter;

    /**
     * Labels name.
     *
     * @var string|bool
     * @access public
     */
    public $labels;

    /**
     * Main Data Model name.
     *
     * @var string
     * @access protected
     */
    protected $model;

    /**
     * Controller meta data.
     *
     * @var array
     * @access protected
     */
    protected $meta = array();

    /**
     * Action output cache.
     *
     * Example format: [action:string -> lifetime:integer(seconds)]
     *
     * @var array
     * @access protected
     */
    protected $cachingOutput = array();

    /**
     * Application instance.
     *
     * @var Core\Silla
     */
    protected $environment;

    /**
     * Base Controller Constructor definition.
     *
     * @param Core\Silla $environment Silla.IO application instance.
     *
     * @access public
     */
    public function __construct(Core\Silla $environment)
    {

        $this->environment = $environment;
        $viewsPaths = $environment->configuration()->paths('views');

        $this->meta = array(
            'controller' => $environment->request()->controller(),
            'action'     => $environment->request()->action(),
            'paths'      => array(
                'views'   => $viewsPaths['templates'] . $environment->request()->controller() . DIRECTORY_SEPARATOR,
                'layouts' => $viewsPaths['layouts'],
            ),
            'filters' => array(
                'before' => array(),
                'after'  => array(),
            ),
        );

        $this->labels          =
            $this->labels ? $this->labels : $this->meta['controller'];
        $this->rendererAdapter =
            $this->rendererAdapter ? $this->rendererAdapter : $environment->configuration()->RENDER['adapter'];


        if ($this->rendererAdapter) {
            $this->renderer = new Core\Modules\Render\Render(
                $this->rendererAdapter,
                $environment->configuration()->paths('views')
            );

            $this->setOutputDefaultHeaders();

            $defaultLayout = 'default';
            $defaultView   = $this->meta['controller'] . DIRECTORY_SEPARATOR . $this->meta['action'];

            if ($this->renderer->isLayout($defaultLayout)) {
                $this->renderer->setLayout($defaultLayout);
            }

            if ($this->renderer->isView($defaultView)) {
                $this->renderer->setView($defaultView);
            }
        }


    }

    /**
     * Executes an controller action.
     *
     * @param string                 $action  Action name.
     * @param Modules\Http\Request $request Request object.
     *
     * @return void
     */
    final public function __executeAction($action, Modules\Http\Request $request)
    {
        $cache = array(
            'lifetime' => $this->getOutputCachingLifetime($action),
        );

        if ($cache['lifetime']) {
            $cache['id'] = $this->generateOutputCacheId($request);

            /* Fetch from cache */
            $output = $this->environment->cache()->fetch($cache['id']);

            if (!$output['content']) {
                $this->executeBeforeFilters($action);
                $this->$action($request);
                $this->executeAfterFilters($action);

                $output['content'] = $this->renderer->getOutput();
                $output['headers'] = $this->environment->response()->getHeaders();

                /* Store in cache */
                $this->environment->cache()->store($cache['id'], $output, $cache['lifetime']);
            } else {
                $this->renderer->setOutput($output['content']);
                $this->environment->response()->addHeaders($output['headers']);
                $this->renderer->render();
            }
        } else {
            $this->executeBeforeFilters($action);
            $this->$action($request);
            $this->executeAfterFilters($action);
        }
    }

    /**
     * Handles 404 pages.
     *
     * Displayed when a requested controller action does not exist.
     *
     * @param Modules\Http\Request $request Request object.
     *
     * @access public
     *
     * @return void
     */
    public function actionNotFound(Modules\Http\Request $request)
    {
        if ($this->renderer) {
            $this->renderer->setLayout('404');
            $this->renderer->setView(null);
            $this->renderer->set('_controller', 'base');
            $this->renderer->set('_action', '404');
            $this->renderer->set('_labels', Core\Helpers\YAML::getAll('globals'));

            $this->environment->response()->setHttpResponseCode(404);
        }
    }

    /**
     * Global 404 page.
     *
     * Displayed when a requested controller does not exist.
     *
     * @param Modules\Http\Request $request Request object.
     *
     * @static
     * @access public
     * @final
     *
     * @return void
     */
    final public static function resourceNotFound(Modules\Http\Request $request)
    {
        if (Core\Config()->RENDER['adapter']) {
            $renderer = new Core\Modules\Render\Render(
                'Core\Modules\Render\Adapters\\' . Core\Config()->RENDER['adapter']
            );

            self::setOutputDefaultHeaders();
            $renderer = self::assignVariablesToRender($renderer);
            $renderer->set('_controller', 'base');
            $renderer->set('_action', '404');
            $renderer->set('_labels', Core\Helpers\YAML::getAll('globals'));
            $renderer->setLayout('404');

            $renderer->setView(null);
            $output = $renderer->render();

            Core\Router()->response->setHttpResponseCode(404);
            Core\Router()->response->setContent($output);
        }
    }

    /**
     * Generate cache identifier value.
     *
     * @param Modules\Http\Request $request Request object.
     *
     * @see    md5(), implode()
     * @return string
     */
    protected static function generateOutputCacheId(Modules\Http\Request $request)
    {
        return md5('_silla_' . implode('', $request->get()));
    }

    /**
     * Set default response HTTP headers.
     *
     * @return void
     */
    protected function setOutputDefaultHeaders()
    {
        $this->environment->response()->addHeaders(array(
            'X-Powered-By: Silla.IO',
            'X-Frame-Options: SAMEORIGIN',
        ));
    }

    /**
     * Adds methods which will be executed before any controller action.
     *
     * @param array $methods Array with method names to be executed.
     * @param array $scope   Execution scope conditions.
     *
     * @access protected
     * @final
     *
     * @return void
     */
    final protected function addBeforeFilters(array $methods, array $scope = array())
    {
        $this->meta['filters']['before'] = array_merge(
            $this->meta['filters']['before'],
            array(
                array(
                    'callbacks' => $methods,
                    'conditions' => array(
                        'except' => isset($scope['except']) ? (array)$scope['except'] : array(),
                        'only' => isset($scope['only'])     ? (array)$scope['only']   : array(),
                    ),
                ),
            )
        );
    }

    /**
     * Adds methods which will be executed after any controller action.
     *
     * @param array $methods Array with method names to be executed.
     * @param array $scope   Execution scope conditions.
     *
     * @access protected
     * @final
     *
     * @return void
     */
    final protected function addAfterFilters(array $methods, array $scope = array())
    {
        $this->meta['filters']['after'] = array_merge(
            $this->meta['filters']['after'],
            array(
                array(
                    'callbacks' => $methods,
                    'conditions' => array(
                        'except' => isset($scope['except']) ? (array)$scope['except'] : array(),
                        'only' => isset($scope['only'])     ? (array)$scope['only']   : array(),
                    ),
                ),
            )
        );
    }

    /**
     * Gets current controller name.
     *
     * @access protected
     * @final
     *
     * @return string
     */
    final protected function getControllerName()
    {
        return $this->meta['controller'];
    }

    /**
     * Gets current action name.
     *
     * @access protected
     * @final
     *
     * @return string
     */
    final protected function getActionName()
    {
        return $this->meta['action'];
    }

    /**
     * Gets controller main model name.
     *
     * @access private
     * @final
     *
     * @return string
     */
    final protected function getModelName()
    {
        return $this->model;
    }

    /**
     * Renders the assigned template.
     *
     * Manages static template caching. See see http://www.smarty.net/docs/en/caching.tpl
     *
     * @access protected
     *
     * @return void
     */
    protected function processOutput()
    {
        $this->assignContentVariables();
        $this->renderer->render();
    }

    /**
     * Renders a partial template specified by its name.
     *
     * @param string $name Name of the partial template for render.
     *
     * @access protected
     * @final
     *
     * @return mixed
     */
    final protected function getPartialOutput($name)
    {
        $this->assignContentVariables();

        return $this->renderer->parse($name);
    }

    /**
     * Retrieves the output cache lifetime for an action.
     *
     * @param string $action Action name.
     *
     * @return integer
     */
    private function getOutputCachingLifetime($action)
    {
        if (isset($this->cachingOutput[$action]) && is_int($this->cachingOutput[$action])) {
            return $this->cachingOutput[$action] > 0 ? $this->cachingOutput[$action] : 0;
        }

        return 0;
    }

    /**
     * Executes the queued before action filters.
     *
     * @param string $action Current action.
     *
     * @access private
     * @throws \BadMethodCallException When specifying non-existing method.
     *
     * @return void
     */
    private function executeBeforeFilters($action)
    {
        $this->addAfterFilters(array('processOutput'));

        if (!empty($this->meta['filters']['before'])) {
            foreach ($this->meta['filters']['before'] as $filters) {
                foreach ($filters['callbacks'] as $callback) {
                    if (in_array($action, $filters['conditions']['except'], true)) {
                        continue;
                    }

                    $only = isset($filters['conditions']['only']) &&
                        !empty($filters['conditions']['only']) &&
                        is_array($filters['conditions']['only']);

                    if (!$only || ($only && in_array($action, $filters['conditions']['only'], true))) {
                        if (method_exists($this, $callback)) {
                            $this->$callback();
                        } else {
                            throw new \BadMethodCallException(
                                'Before filter method ' . get_class($this) . "::{$callback}() is not defined."
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Executes the queued after action filters.
     *
     * @param string $action Current action.
     *
     * @access private
     * @throws \BadMethodCallException When specifying non-existing method.
     *
     * @return void
     */
    private function executeAfterFilters($action)
    {
        if (!empty($this->meta['filters']['after'])) {
            foreach ($this->meta['filters']['after'] as $filters) {
                foreach ($filters['callbacks'] as $callback) {
                    if (in_array($action, $filters['conditions']['except'], true)) {
                        continue;
                    }

                    $only = isset($filters['conditions']['only']) &&
                        !empty($filters['conditions']['only']) &&
                        is_array($filters['conditions']['only']);

                    if (!$only || ($only && in_array($action, $filters['conditions']['only'], true))) {
                        if (method_exists($this, $callback)) {
                            $this->$callback();
                        } else {
                            throw new \BadMethodCallException(
                                'After filter method ' . get_class($this) . "::{$callback}() is not defined."
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Load labels for the controller.
     *
     * @access private
     *
     * @return array
     */
    private function loadLabels()
    {
        $helper = new Core\Helpers\YAML($this->environment);

        if ($this->environment->configuration()->CACHE['labels']) {
            $key    = '_silla_'
                . $this->environment->configuration()->paths('mode')
                . '_labels_'
                . $this->environment->registry()->get('locale')
                . $this->labels;
            $labels = $this->environment->cache()->fetch($key);

            if (!$labels) {
                $labels = $helper->getExtendWithGlobals($this->labels);
                $this->environment->cache()->store($key, $labels);
            }
        } else {
            $labels = $helper->getExtendWithGlobals($this->labels);
        }

        return $labels;
    }

    /**
     * Assign easy to use template vars.
     *
     * @access private
     * @see    get_object_vars()
     *
     * @return void
     */
    private function assignContentVariables()
    {
        $this->renderer = self::assignVariablesToRender($this->renderer);
        $this->renderer->set('_controller', $this->meta['controller']);
        $this->renderer->set('_action', $this->meta['action']);

        /* Load local labels */
        if ($this->labels) {
            $this->renderer->set('_labels', $this->loadLabels());
        }

        $locals = get_object_vars($this);

        unset(
            $locals['renderer'],
            $locals['rendererAdapter'],
            $locals['meta'],
            $locals['labels'],
            $locals['cachingOutput']
        );

        foreach ($locals as $key => $value) {
            $this->renderer->set($key, $value);
        }
    }

    /**
     * Assigns common template engine vars.
     *
     * @param Modules\Render\Render $renderer Render module object.
     *
     * @return Modules\Render\Render
     */
    private function assignVariablesToRender(Modules\Render\Render &$renderer)
    {
        $renderer->set('_mode', $this->environment->configuration()->paths('mode'));
        $renderer->set('_registry', $this->environment->registry());
        $renderer->set('_config', $this->environment->configuration());
        $renderer->set('_session', $this->environment->session());
        $renderer->set('_assets', $renderer->assets());
        $renderer->set('_urls', $this->environment->configuration()->urls());
        $renderer->set('_paths', $this->environment->configuration()->paths());
        $renderer->set('_request', $this->environment->request());
        $renderer->set('_get', $this->environment->request()->get());
        $renderer->set('_post', $this->environment->request()->post());

        return $renderer;
    }
}
