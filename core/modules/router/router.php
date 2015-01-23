<?php
/**
 * Router class definition.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Router
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Router;

use Core;

/**
 * Router Class definition.
 */
final class Router
{
    /**
     * Request object.
     *
     * @var Request
     * @access public
     */
    public $request;

    /**
     * Response object.
     *
     * @var Response
     * @access public
     */
    public $response;

    /**
     * Reference to the current instance of the Routes object.
     *
     * @var Router
     * @access private
     * @static
     */
    private static $instance = null;

    /**
     * All routing routes container.
     *
     * @var Routes
     * @access private
     */
    private $routes;

    /**
     * Constructor. Filters all input.
     *
     * @access private
     */
    private function __construct()
    {
        if (get_magic_quotes_gpc()) {
            self::disableMagicQuotes();
        }
    }

    /**
     * Dispatches the processed request.
     *
     * @param Request $request Router Request object.
     * @param Routes  $routes  Router Routes object.
     *
     * @access public
     * @throws \LogicException Router is not initialized properly. Specify Routing routes.
     *
     * @return void
     */
    public function dispatch(Request &$request, Routes &$routes)
    {
        $this->routes  = $routes;
        $this->request = $request;
        $this->response = new Response;

        self::verifyRequest($request);

        $namespace = $request->mode('namespace');

        $controller = "\\{$namespace}\\Controllers\\" . $request->controller();

        if (class_exists($controller)) {
            $controller = new $controller;
            $action = $request->action();

            /* Check if there is such action implemented and filter for magic methods like __construct, etc. */
            if (is_callable(array($controller, $action)) && false === strpos($action, '__')) {
                $controller->__executeAction($action, $request);
            } else {
                $controller->__executeAction('actionNotFound', $request);
            }

            $this->response->setContent($controller->renderer->getOutput());
        } else {
            Core\Base\Controller::resourceNotFound($request);
        }
    }

    /**
     * Generates an application url.
     *
     * @param array $options Array of options.
     *
     * @access public
     * @uses   Core\Registry();
     * @uses   Core\Config();
     *
     * @return string
     */
    public function toUrl(array $options)
    {
        static $_cache;

        $_cache_key = md5(serialize($options));

        $options = array_merge(array('controller' => $this->request->controller()), $options);

        if (!isset($_cache[$_cache_key])) {
            $route = $this->routes->extractUrl($options);
            $route['pattern'] = $this->routes->toRoute($route['pattern']);

            foreach ($route['pattern'] as $key => $url_element) {
                if (isset($url_element{0}) && ($url_element{0} === Core\Config()->ROUTER['variables_prefix'])) {
                    $option_key = str_replace(Core\Config()->ROUTER['variables_prefix'], '', $url_element);
                    $options[$option_key] = isset($options[$option_key]) ? $options[$option_key] : null;

                    $route['pattern'][$key] = $options[$option_key];
                }
            }

            foreach ($options as $key => $option) {
                if (array_key_exists($key, $route['maps_to'])) {
                    unset($options[$key]);
                }
            }

            if (Core\Config()->SESSION['transparency']) {
                $options[Core\Config()->SESSION['parameter']] = Core\Session()->getKey();
            }

            $_prefix = '';

            if (!Core\Config()->ROUTER['rewrite']) {
                $_prefix = '?_path=';
            }

            $mode = isset($options['_mode']) ? Core\Config()->modes($options['_mode']) : $this->request->mode();
            $mode['url'] = $mode['url'] ? $mode['url'] . Core\Config()->ROUTER['separator'] : '';

            $_cache[$_cache_key] =
                $_prefix . $mode['url'] . rtrim(
                    implode(
                        Core\Config()->ROUTER['separator'],
                        $route['pattern']
                    ),
                    Core\Config()->ROUTER['separator']
                ) . ((!empty($options)) ? '?' . http_build_query($options, false, '&amp;') : '');
        }

        return $_cache[$_cache_key];
    }

    /**
     * Returns an instance of the router object.
     *
     * @access public
     * @final
     * @static
     *
     * @return Router
     */
    final public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    /**
     * Cloning of Router is disallowed.
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(__CLASS__ . ' cannot be cloned! It is singleton.', E_USER_ERROR);
    }

    /**
     * Cookie wrapper for storing a cookie.
     *
     * @param string  $name     Name of the cookie.
     * @param mixed   $data     Data to store in the cookie (must be less than 4KB).
     * @param integer $duration Time before expiration in milliseconds.
     * @param boolean $raw      Whether to use raw cookie storage.
     *
     * @access public
     * @uses   Core\Config()
     *
     * @return boolean
     */
    public function setCookie($name, $data, $duration = 0, $raw = false)
    {
        return setcookie(
            $name,
            $raw ? $data : serialize($data),
            $duration ? time() + $duration : false,
            Core\Config()->urls('relative'),
            $this->request->meta('SERVER_NAME'),
            Core\Config()->urls('protocol') === 'https',
            true
        );
    }

    /**
     * Cookie wrapper for removal of a cookie.
     *
     * @param string $name Name of the cookie to remove.
     *
     * @access public
     * @uses   Core\Config()
     *
     * @return boolean
     */
    public function deleteCookie($name)
    {
        return setcookie(
            $name,
            '',
            time() - 3600,
            Core\Config()->urls('relative'),
            $this->request->meta('SERVER_NAME'),
            Core\Config()->urls('protocol') === 'https',
            true
        );
    }

    /**
     * Extract current mode.
     *
     * @param string $httpRequestString HTTP qeuery request string.
     *
     * @return array
     */
    public static function getMode($httpRequestString)
    {
        $modes = Core\Config()->modes();

        foreach ($modes as $mode) {
            if ($mode['url'] && 0 === strpos($httpRequestString, $mode['url'])) {
                return $mode;
            }
        }

        /* Default mode */
        return end($modes);
    }

    /**
     * Parses a request query string.
     *
     * @param string $httpRequestString Request string.
     * @param Routes $routes            Request routing routes.
     *
     * @static
     * @uses Core\Utils::parseHttpRequestString()
     *
     * @return array
     */
    public static function parseRequestQueryString($httpRequestString, Routes $routes)
    {
        if (Core\Config()->mode('url')) {
            $httpRequestString = trim(
                Core\Utils::replaceFirstOccurance(Core\Config()->mode('url'), '', $httpRequestString),
                Core\Config()->ROUTER['separator']
            );
        } else {
            $httpRequestString = trim(
                $httpRequestString,
                Core\Config()->ROUTER['separator']
            );
        }

        $requestElements = Core\Utils::parseHttpRequestString($httpRequestString, Core\Config()->ROUTER['separator']);

        $route     = $routes->extractRoute($requestElements);
        $routedUrl = $routes->toRoute($route['pattern']);
        $elements  = $route['maps_to'];

        foreach ($route['maps_to'] as $role => $value) {
            if ('*' === $value) {
                $_element = array_search(Core\Config()->ROUTER['variables_prefix'] . $role, $routedUrl);
                $elements[$role] = isset($requestElements[$_element]) ? $requestElements[$_element] : null;
            }
        }

        return $elements;
    }

    /**
     * Disabling magic quotes at runtime.
     *
     * @access public
     * @static
     *
     * @return void
     */
    public static function disableMagicQuotes()
    {
        $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

        while (list($key, $val) = each($process)) {
            foreach ($val as $k => $v) {
                unset($process[$key][$k]);
                if (is_array($v)) {
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = &$process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }

        unset($process);
    }

    /**
     * Verifies validity of the request.
     *
     * @param Request $request Current router request.
     *
     * @static
     * @throws \UnexpectedValueException When there is no token or it does not match.
     *
     * @return void
     */
    protected static function verifyRequest(Request $request)
    {
        if ($request->is('post') || $request->is('put') || $request->is('delete') || $request->is('patch')) {
            if (!$request->variables('_token') || $request->variables('_token') !== Core\Session()->get('_token')) {
                Core\Router()->response->addHeader($request->type() . ' 403 Forbidden', true, 403);

                throw new \UnexpectedValueException('Request token does not match.');
            }
        }
    }
}
