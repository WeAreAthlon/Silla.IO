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

namespace Core\Modules\Http;

use Core;
use Aura;

/**
 * Router Class definition.
 */
final class Router
{
    /**
     * Request object.
     *
     * @var Request
     */
    public $request;

    /**
     * Response object.
     *
     * @var Response
     */
    public $response;

    /**
     * Reference to the current instance of the Routes object.
     *
     * @var Router
     * @static
     */
    private static $instance = null;

    /**
     * All routing routes container.
     *
     * @var Routes
     */
    private $routes;

    /**
     * @var \Aura\Router\Router
     */
    private $router;

    /**
     * Filters all input.
     */
    private function __construct()
    {
        if (get_magic_quotes_gpc()) {
            self::disableMagicQuotes();
        }

        $routerFactory = new Aura\Router\RouterFactory;
        $this->router = $routerFactory->newInstance();
    }

    /**
     * Dispatches the processed request.
     *
     * @param Request                      $request Router Request object.
     * @param \Aura\Router\RouteCollection  $routes  Collection of routes.
     *
     * @throws \InvalidArgumentException Request token does not match.
     *
     * @return void
     */
    public function dispatch(Request $request, Aura\Router\RouteCollection $routes)
    {
        $this->routes   = $routes;
        $this->request  = $request;
        $this->response = new Response;

        if (Core\Session()->get('_token')) {
            $this->request->setToken(Core\Session()->get('_token'));
        } else {
            $this->request->regenerateToken();
            Core\Session()->set('_token', $this->request->token());
        }

        if (!$request->isValid()) {
            $this->response->setHttpResponseCode(403);
            throw new \InvalidArgumentException('Request token does not match.');
        }

        $this->router->setRoutes($routes);

        if ($request->is('post') && $request->post('_method')) {
            $request->setMethod($request->post('_method'));
        }

        $route = $this->match($request);

        $request->setRoutedElements($route->params);

        $namespace = $request->mode('namespace');

        $controller = "\\{$namespace}\\Controllers\\" . $request->controller();

        if (class_exists($controller)) {
            $controller = new $controller;
            $action     = $request->action();

            /* Check if there is such action implemented and filter for magic methods like __construct, etc. */
            if (is_callable(array($controller, $action)) && false === strpos($action, '__')) {
                $controller->__executeAction($action, $request);
            } else {
                $controller->__executeAction('actionNotFound', $request);
            }

            $this->response->setContent($controller->renderer->getOutput());
            $this->response->addHeader('Content-Type: ' . $controller->renderer->getOutputContentType());
        } else {
            Core\Base\Controller::resourceNotFound($request);
        }
    }

    /**
     * Matches a routing route.
     *
     * @param \Core\Modules\Http\Request $request
     *
     * @throws \RuntimeException When matching failed.
     *
     * @return \Aura\Router\Route|false
     */
    private function match(Request $request)
    {
        $route = $this->router->match($request->path(), $request->meta());

        if (!$route) {
            $failure = $this->router->getFailedRoute();

            if ($failure->failedMethod()) {
                $this->response->setHttpResponseCode(405);
                throw new \RuntimeException('The route matching failed on the allowed HTTP methods.');
            } elseif ($failure->failedAccept()) {
                $this->response->setHttpResponseCode(406);
                throw new \RuntimeException('The route matching failed on the available content-types.');
            } else {
                $this->response->setHttpResponseCode(404);
                throw new \RuntimeException('The route does not exist.');
            }
        }

        return $route;
    }


    /**
     * Generates an application url.
     *
     * @param array $options Array of options.
     *
     * @uses   Core\Registry();
     * @uses   Core\Config();
     *
     * @return string
     */
    public function toUrl(array $options)
    {
        static $_cache;

        $_cache_key = md5(json_encode($options));

        if (!isset($_cache[$_cache_key])) {
            $mode = isset($options['_mode']) ? Core\Config()->modes($options['_mode']) : $this->request->mode();
            unset($options['_mode']);

            if ($mode != $this->request->mode()) {
                $namespace = $this->request->mode('namespace');
                $routes = "\\{$namespace}\\Configuration\\Routes";

                $this->router->setRoutes(new $routes());
            }

            $route = isset($options['for']) ? $options['for'] : 'default';
            $options['format'] = isset($options['format']) ? $options['format'] : '';
            $options['id'] = isset($options['id']) ? $options['id'] : '';

            unset($options['for']);

            if (isset($options['resource']) && !empty($options['resource'])) {
                $resource = new \ReflectionClass(get_class($options['resource']));
                if (isset($options['action']) && !empty($options['action'])) {
                    $options['id'] = $options['resource']->{$options['resource']::primaryKeyField()};
                    $options['resource'] = $resource->getShortName();
                    $route = "{$options['resource']}.{$options['action']}";
                } else {
                    $route = "{$options['resource']}.read";
                }
            }

            if (Core\Config()->SESSION['transparency']) {
                $options[Core\Config()->SESSION['parameter']] = Core\Session()->getKey();
            }

            $_prefix = '';

            if (!Core\Config()->ROUTER['rewrite']) {
                $_prefix = '?';
            }

            $route = htmlspecialchars($this->router->generate($route, $options), ENT_QUOTES, 'UTF-8');
            $_cache[$_cache_key] = $_prefix . $mode['url'] . $route;
        }

        return $_cache[$_cache_key];
    }

    /**
     * Generates an application full url.
     *
     * @param array $options Array of options.
     *
     * @uses   toUrl
     * @uses   Core\Config();
     *
     * @return string
     */
    public function toFullUrl(array $options)
    {
        return Core\Config()->urls('full') . $this->toUrl($options);
    }

    /**
     * Returns an instance of the router object.
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
     * @uses Core\Utils::parseHttpRequestString()
     *
     * @return array
     */
    public static function parseRequestQueryString($httpRequestString, Routes $routes)
    {
        if (Core\Config()->mode('url')) {
            $httpRequestString = trim(
                Core\Utils::replaceFirstOccurrence(Core\Config()->mode('url'), '', $httpRequestString),
                Core\Config()->ROUTER['separator']
            );
        } else {
            $httpRequestString = trim(
                $httpRequestString,
                Core\Config()->ROUTER['separator']
            );
        }

        $requestElements = Core\Utils::parseHttpRequestString($httpRequestString, Core\Config()->ROUTER['separator']);

        return $requestElements;
    }

    /**
     * Disabling magic quotes at runtime.
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
                    $process[]                       = &$process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }

        unset($process);
    }

    /**
     * Retrieve the relative request query string.
     *
     * @param string $path Request query string.
     *
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = explode('?', $path);

        if (!Core\Config()->ROUTER['rewrite'] && isset($path[1])) {
            $path = explode('&', $path[1]);
        }

        $path            = $path[0];
        $applicationPath = Core\Config()->urls('relative');

        if ($applicationPath !== '/') {
            $path = '/' . str_replace($applicationPath, '', $path);
        }

        return ltrim($path, '/');
    }
}
