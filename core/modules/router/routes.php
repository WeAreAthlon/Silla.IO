<?php
/**
 * Routes Routes.
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
 * Routes Class definition.
 */
final class Routes
{
    /**
     * Stored all added routes.
     *
     * @var array
     * @access private
     */
    private $routes = array();

    /**
     * Representation of the Silla.IO mode.
     *
     * @var array
     */
    private $mode = array();

    private $routesSource = array();

    /**
     * Init Routes.
     *
     * @param array $mode Silla.IO mode.
     *
     * @uses Core\Cache()
     *
     * @access private
     */
    public function __construct()
    {
        \Silla::$routesSource['/'] = \Spyc::YAMLLoad('app/routes.yaml');

        if (Core\Config()->CACHE['routes']) {
            $routes = Core\Cache()->fetch('app_routes');

            if (!$routes) {
                $routes = self::loadRoutes();
                Core\Cache()->store('app_routes', $routes);
            }
        } else {
            $routes = self::loadRoutes();
        }

        foreach ($routes as $route) {
            $this->add(array(
                'pattern' => $route['route'],
                'maps_to' => $route['maps'],
                'package' => $route['package']
            ));
        }
    }

    /**
     * Adds a route.
     *
     * @param array $val Example ['url' => string, 'maps_to' => string].
     *
     * @access public
     *
     * @return boolean
     */
    public function add(array $val)
    {
        $this->routes[] = $val;

        return true;
    }

    /**
     * Get all added routes in reversed order.
     *
     * @param array $package Filter routes by package
     *
     * @access public
     *
     * @return array
     */
    public function getAll($package = array())
    {
        if(!empty($package)) {
            return array_filter($this->routes, function($route) use($package) {
                return $route['package'] == $package['name'];
            });
        }

        return $this->routes;
    }

    /**
     * Select the best route from all registered routes according url address.
     *
     * @param array $url URL elements array.
     *
     * @access public
     *
     * @return array
     */
    public function extractRoute(array $url)
    {
        $route = null;
        $routes = $this->getAll();
        $default_route = end($routes);

        foreach ($url as $position => $element) {
            foreach ($routes as $key => $route) {
                $route_elements = $this->toRoute($route['pattern']);
                $route_elements[$position] = isset($route_elements[$position]) ? $route_elements[$position] : null;
                if (
                    (($route_elements[$position] === '')
                    || ($route_elements[$position]{0} !== Core\Config()->ROUTER['variables_prefix']))
                    && ($route_elements[$position] !== $element)
                ) {
                    unset($routes[$key]);
                }
            }
        }

        if ($routes) {
            $route = reset($routes);
            $routed_url = $this->toRoute($route['pattern']);
            $test_route = $route['maps_to'];

            foreach ($route['maps_to'] as $role => $value) {
                if ('*' === $value) {
                    $test_route[$role] = $url[array_search(
                        Core\Config()->ROUTER['variables_prefix'] . $role,
                        $routed_url
                    )];
                }
            }

            $_controller = $route['package'] . '\Controllers\\' . $test_route['controller'];

            if (!class_exists($_controller)) {
                $route = next($routes);
                $_controller = $route['package'] . '\Controllers\\' . $route['maps_to']['controller'];

                if (!method_exists($_controller, $test_route['controller'])) {
                    $route = next($routes);
                }
            }
        }

        return $route ? $route : $default_route;
    }

    /**
     * Select the best routing rules according to url mapping.
     *
     * @param array $url_mapping Representation of URL address.
     * @param array $package Search in package routes
     *
     * @access public
     *
     * @return array
     */
    public function extractUrl(array $url_mapping, $package = array())
    {
        $routes = $this->getAll($package);
        $default_route = end($routes);
        $url_mapping_count = count($url_mapping);

        foreach ($routes as $key => $route) {
            if (
                $url_mapping_count != count($route['maps_to'])
                || array_keys($url_mapping) !== array_keys($route['maps_to'])
            ) {
                unset($routes[$key]);
            } else {
                foreach ($route['maps_to'] as $role => $element) {
                    if (($element !== '*') && ($element !== $url_mapping[$role])) {
                        unset($routes[$key]);
                        break;
                    }
                }
            }
        }

        return empty($routes) ? $default_route : reset($routes);
    }

    /**
     * Prepares the url for the routing.
     *
     * @param string $pattern String representation of the url.
     *
     * @access private
     *
     * @return array
     */
    public function toRoute($pattern)
    {
        return explode('/', trim($pattern, '/'));
    }

    /**
     * Get mode.
     *
     * @return array
     */
    public function mode()
    {
        return $this->mode;
    }

    /**
     * Load router routing routes.
     *
     * @param array $mode Silla.IO mode.
     *
     * @access private
     * @static
     * @uses   \Spyc
     *
     * @return array
     */
    private static function loadRoutes()
    {
        $routes = [];

        foreach(\Silla::$routesSource as $preffix=>$r) {
            $routes = array_merge($routes, array_map(function($item) use($preffix) {
                $item['package'] = $preffix;
                return $item;
            }, $r));
        }

        return $routes;
    }
}
