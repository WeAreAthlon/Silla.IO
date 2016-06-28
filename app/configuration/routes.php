<?php
/**
 * Routes Configuration.
 *
 * @package    Silla.IO
 * @subpackage App\Configuration
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */
namespace App\Configuration;

use Core;
use Core\Base;

/**
 * Class Routes.
 */
final class Routes extends Core\Modules\Http\Routes
{
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
        /* Home page */
        $this->routes->addGet('home', '/')
            ->addValues(array('controller' => 'home', 'action' => 'index'));

        /* Default routes */
        $this->routes->addGet('index', '/{controller}');
        $this->routes->addGet('default', '/{controller}/{action}');
    }
}