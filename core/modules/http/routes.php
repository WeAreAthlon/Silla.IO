<?php
/**
 * Router Routes.
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
 * Routes Class definition.
 */
abstract class Routes
{
    /**
     * Stored all added routes.
     *
     * @var array
     * @access private
     */
    protected $routes = array();

    /**
     * Representation of the Silla.IO mode.
     *
     * @var array
     */
    private $mode = array();

    /**
     * Init Routes.
     *
     * @uses Core\Cache()
     *
     * @access private
     */
    public function __construct()
    {
        $routeFactory = new Aura\Router\RouteFactory;
        $this->routes = new Aura\Router\RouteCollection($routeFactory);

        if (Core\Config()->CACHE['routes']) {
            $this->routes = Core\Cache()->fetch($mode['location'] . '_routes');

            if (!$this->routes) {
                $this->routes = serialize($this->getAll());
                Core\Cache()->store($mode['location'] . '_routes', $this->routes);
            }
        } else {
            $this->routes = $this->getAll();
        }

        $this->setup();
    }

    /**
     * Setup routes.
     *
     * @example $this->routes->add();
     * @see     https://github.com/auraphp/Aura.Router
     *
     * @return void
     */
    public function setup()
    {
    }


    /**
     * Retrieve all routes.
     *
     * @return Aura\Router\RouteCollection
     */
    public function getAll()
    {
        return $this->routes;
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
}
