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
namespace CMS\Configuration;

use Core;
use Core\Base;
use Core\Modules;

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
        $this->routes->addGet('authentication_form', '/')
            ->addValues(array('controller' => 'Authentication', 'action' => 'form'));

        /* Login */
        $this->routes->addPost('authentication_login', '/authentication/login')
            ->addValues(array('controller' => 'Authentication', 'action' => 'login'));

        /* Logout */
        $this->routes->addGet('authentication_logout', '/authentication/logout')
            ->addValues(array('controller' => 'Authentication', 'action' => 'logout'));

        /* Forgotten password */
        $this->routes->addGet('authentication_reset_form', '/authentication/reset')
            ->addValues(array('controller' => 'Authentication', 'action' => 'reset_form'));
        $this->routes->addPost('password_reset', '/authentication/reset')
            ->addValues(array('controller' => 'Authentication', 'action' => 'reset'));

        /* Resources */
        $this->routes->attachResource('CMSUser', '/cms-users');
        $this->routes->attachResource('CMSUserRole', '/cms-user-roles');
        $this->routes->attachResource('CMSHelp', '/cms-help');
     
        /* Default routes */
        $this->routes->addGet('default', '/{controller}/{action}');
    }
}