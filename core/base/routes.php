<?php
/**
 * HTTP Router Routes Base class.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;
use Aura;

/**
 * Class Configuration Definition.
 */
abstract class Routes
{
    /**
     * Routes.
     *
     * @var Aura\Router\RouteCollection
     */
    protected $routes;

    /**
     * Constructor. Setup router instance.
     */
    public function __construct()
    {
        $route_factory = new Aura\Router\RouteFactory;
        $this->routes = new Aura\Router\RouteCollection($route_factory);

        $this->setup();
    }

    /**
     * Setup routes.
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
    public function routes()
    {
        return $this->routes;
    }
}
