<?php
/**
 * Database decorator interface.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Interfaces;

use Core;

/**
 * Interface iDecorator definition.
 */
interface Decorator
{
    /**
     * Decorating method.
     *
     * @param Core\Base\Model $resource Currently processed resource.
     *
     * @static
     *
     * @return void
     */
    public static function decorate(Core\Base\Model $resource);
}
