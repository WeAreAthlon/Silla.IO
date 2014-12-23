<?php
/**
 * Database decorator interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
