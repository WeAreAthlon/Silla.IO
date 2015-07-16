<?php
/**
 * Formatting Decorator Interface.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Rozaliya Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators\Interfaces;

/**
 * Formatting Decorator for text formatting management of object fields
 */
interface Formatting
{
    /**
     * Formalize fields container.
     *
     * @static
     *
     * @return array
     */
    public static function formalizeFields();
}
