<?php
/**
 * Database observer interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Interfaces
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB\Interfaces;

use Core;

/**
 * Database observer interface definition.
 */
interface Observer
{
    /**
     * Attach method.
     *
     * @param Core\Base\Model $object   Amended object.
     * @param string          $event    Event name.
     * @param string          $callback Callback function name.
     *
     * @static
     *
     * @return void
     */
    public static function on(Core\Base\Model $object, $event, $callback);

    /**
     * Detach method.
     *
     * @param Core\Base\Model $object   Amended object.
     * @param string          $event    Event name.
     * @param string          $callback Callback function name.
     *
     * @static
     *
     * @return void
     */
    public static function detach(Core\Base\Model $object, $event, $callback);

    /**
     * Invocation method.
     *
     * @param Core\Base\Model $object    Amended object.
     * @param string          $event     Event name.
     * @param array           $arguments Additional arguments.
     *
     * @static
     *
     * @return void
     */
    public static function fire(Core\Base\Model $object, $event, array $arguments = array());
}
