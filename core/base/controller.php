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
use Core\Modules\Router\Request;

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
     * @var array|bool
     * @access public
     */
    public $labels;

    /**
     * Controller meta data.
     *
     * @var array
     * @access protected
     */
    protected $meta = array(
        'filters' => array(
            'before' => array(),
            'after'  => array(),
        ),
    );

    /**
     * Default layout name.
     *
     * @var string
     */
    protected $layout = 'default';

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
     * Base Controller Constructor definition.
     *
     * @access public
     */
    public function __construct()
    {
        $viewsPaths = Core\Config()->paths('views');

        $this->meta = array_merge($this->meta, array(
            'controller' => Core\Router()->request->controller(),
            'action'     => Core\Router()->request->action(),
            'paths'      => array(
                'views'   => $viewsPaths['templates'] . Core\Router()->request->controller() . DIRECTORY_SEPARATOR,
                'layouts' => $viewsPaths['layouts'],
            ),
        ));

        $this->labels          = $this->labels ? $this->labels : array($this->meta['controller']);
        $this->rendererAdapter = $this->rendererAdapter ? $this->rendererAdapter : Core\Config()->RENDER['adapter'];

        if ($this->rendererAdapter) {
            $this->renderer = new Core\Modules\Render\Render($this->rendererAdapter, Core\Config()->RENDER['options']);

            static::setOutputDefaultHeaders();

            $defaultLayout = $this->layout;
            $defaultView   = $this->meta['controller'] . DIRECTORY_SEPARATOR . $this->meta['action'];

            if ($this->renderer->isLayout($defaultLayout)) {
                $this->renderer->setLayout($defaultLayout);
            }

            if ($this->renderer->isView($defaultView)) {
                $this->renderer->setView($defaultView);
            }
        }

        $this->addBeforeFilters(array('loadLabels'));
    }

    /**
     * Executes an controller action.
     *
     * @param string  $action  Action name.
     * @param Request $request Request object.
     *
     * @return void
     */
    final public function __executeAction($action, Request $request)
    {
        $cache = array(
            'lifetime' => $this->getOutputCachingLifetime($action),
        );

        if ($cache['lifetime']) {
            $cache['id'] = $this->generateOutputCacheId($request);

            /* Fetch from cache */
            $output = Core\Cache()->fetch($cache['id']);

            if (!$output['content']) {
                $this->executeBeforeFilters($action, $request);
                $this->$action($request);
                $this->executeAfterFilters($action, $request);

                $output['content'] = $this->renderer->getOutput();
                $output['headers'] = Core\Router()->response->getHeaders();

                /* Store in cache */
                Core\Cache()->store($cache['id'], $output, $cache['lifetime']);
            } else {
                $this->renderer->setOutput($output['content']);
                Core\Router()->response->addHeaders($output['headers']);
                $this->renderer->render();
            }
        } else {
            $this->executeBeforeFilters($action, $request);
            $this->$action($request);
            $this->executeAfterFilters($action, $request);
        }
    }

    /**
     * Handles 404 pages.
     *
     * Displayed when a requested controller action does not exist.
     *
     * @param Modules\Router\Request $request Request object.
     *
     * @access public
     *
     * @return void
     */
    public function actionNotFound(Modules\Router\Request $request)
    {
        if ($this->renderer) {
            $this->renderer->setLayout('404');
            $this->renderer->setView(null);
            $this->renderer->set('_controller', 'base');
            $this->renderer->set('_action', '404');
            $this->renderer->set('_labels', self::loadLabelsFile(Core\Config()->mode('name')));

            Core\Router()->response->setHttpResponseCode(404);
        }
    }

    /**
     * Global 404 page.
     *
     * Displayed when a requested controller does not exist.
     *
     * @param Modules\Router\Request $request Request object.
     *
     * @static
     * @access public
     * @final
     *
     * @return void
     */
    final public static function resourceNotFound(Modules\Router\Request $request)
    {
        if (Core\Config()->RENDER['adapter']) {
            $renderer = new Core\Modules\Render\Render(Core\Config()->RENDER['adapter']);

            static::setOutputDefaultHeaders();
            $renderer = self::assignVariablesToRender($renderer);
            $renderer->set('_controller', 'base');
            $renderer->set('_action', '404');
            $renderer->set('_labels', self::loadLabelsFile(Core\Config()->mode('name')));
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
     * @param Modules\Router\Request $request Request object.
     *
     * @see    md5(), implode()
     * @return string
     */
    protected static function generateOutputCacheId(Modules\Router\Request $request)
    {
        return md5('_silla_' . implode('', $request->get()));
    }

    /**
     * Set default response HTTP headers.
     *
     * @static
     *
     * @return void
     */
    protected static function setOutputDefaultHeaders()
    {
        Core\Router()->response->addHeaders(array(
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
                    'callbacks'  => $methods,
                    'conditions' => array(
                        'except' => isset($scope['except']) ? (array)$scope['except'] : array(),
                        'only'   => isset($scope['only']) ? (array)$scope['only'] : array(),
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
                    'callbacks'  => $methods,
                    'conditions' => array(
                        'except' => isset($scope['except']) ? (array)$scope['except'] : array(),
                        'only'   => isset($scope['only']) ? (array)$scope['only'] : array(),
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
     * @param string  $action  Current action.
     * @param Request $request Current Router Request.
     *
     * @access private
     * @throws \BadMethodCallException When specifying non-existing method.
     *
     * @return void
     */
    private function executeBeforeFilters($action, Request $request)
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
                            $this->$callback($request);
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
     * @param string  $action  Current action.
     * @param Request $request Current Router Request.
     *
     * @access private
     * @throws \BadMethodCallException When specifying non-existing method.
     *
     * @return void
     */
    private function executeAfterFilters($action, Request $request)
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
                            $this->$callback($request);
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
     * @return void
     */
    protected function loadLabels()
    {
        $labels = self::loadLabelsFile(Core\Config()->mode('name'));

        foreach ($this->labels as $labelsFile) {
            $labels = Core\Utils::arrayExtend($labels, self::loadLabelsFile($labelsFile));
        }

        $this->labels = $labels;
    }

    /**
     * Load Labels file.
     *
     * @param string $fileName Labels file name.
     *
     * @access private
     *
     * @return array
     */
    private static function loadLabelsFile($fileName)
    {
        if (Core\Config()->CACHE['labels']) {
            $mode   = Core\Config()->paths('mode');
            $key    = '_silla_' . $mode . '_labels_' . Core\Registry()->get('locale') . $fileName;
            $labels = Core\Cache()->fetch($key);

            if (!$labels) {
                $labels = Core\Helpers\YAML::getAll($fileName);
                Core\Cache()->store($key, $labels);
            }
        } else {
            $labels = Core\Helpers\YAML::getAll($fileName);
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
            $this->renderer->set('_labels', $this->labels);
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
     * @access private
     *
     * @return Modules\Render\Render
     */
    private static function assignVariablesToRender(Modules\Render\Render &$renderer)
    {
        $locale =  Core\Registry()->get('locale');
        $language = explode('_', $locale);

        $renderer->set('_mode', Core\Config()->paths('mode'));
        $renderer->set('_registry', Core\Registry());
        $renderer->set('_config', Core\Config());
        $renderer->set('_session', Core\Session());
        $renderer->set('_assets', $renderer->assets());
        $renderer->set('_urls', Core\Config()->urls());
        $renderer->set('_paths', Core\Config()->paths());
        $renderer->set('_request', Core\Router()->request);
        $renderer->set('_get', Core\Router()->request->get());
        $renderer->set('_post', Core\Router()->request->post());
        $renderer->set('_environment', SILLA_ENVIRONMENT);
        $renderer->set('_locale', $locale);
        $renderer->set('_language', current($language));

        return $renderer;
    }
}
