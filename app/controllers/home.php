<?php
/**
 * Home Controller.
 *
 * @package    Silla.IO
 * @subpackage App\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace App\Controllers;

use Core;
use Core\Base;
use Core\Modules\Http\Request;

/**
 * Home controller definition.
 */
class Home extends Base\Controller
{
    /**
     * Index action.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $mdl = new \App\Models\User($this->environment);

        $res = $mdl->save([
            'name' => 'test name',
            'email' => 'testemail'
        ]);

        dd($mdl);
    }
}
