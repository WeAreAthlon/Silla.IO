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
use Aura;

/**
 * Class Routes.
 */
final class Routes extends Base\Routes
{
    /**
     * Setup routes.
     *
     * @return array
     */
    public function setup()
    {
        $this->routes->add('home', '/');
        $this->routes->add('default', '/{controller}/{action}');
    }
}