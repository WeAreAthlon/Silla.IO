<?php
/**
 * App Controller.
 *
 * @package    Silla.IO
 * @subpackage App\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace App\Controllers;

use Core\Base;
use Core\Modules\Http\Request;

/**
 * App controller definition.
 */
class App extends Base\Controller
{
    /**
     * Index action.
     *
     * @param Request $request Current http request.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $this->renderer->assets->add('app/assets/css/styles.css');
        $this->renderer->assets->add('app/assets/js/init.js');
    }
}
