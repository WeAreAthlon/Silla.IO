<?php
/**
 * {$controller|camelize} Controller.
 *
 * @package    Silla.IO
 * @subpackage {$mode}\Controllers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace {$mode}\Controllers;
use Core;
use Core\Base;
use Core\Modules\Router\Request;

/**
 * {$controller|camelize} class definition.
 */
class {$controller|camelize} extends Base\Controller
{
{foreach from=$actions item=action name=actions}
{if $smarty.foreach.actions.iteration neq 1 }

{/if}
    /**
     * {$action|camelize} method.
     *
     * @param Request $request Current router request.
     *
     * @return void
     */
    public function {$action}(Request $request)
    {

    }
{/foreach}
}
