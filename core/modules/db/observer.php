<?php
/**
 * Class Observer.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB;

use Core;

/**
 * Class Observer definition.
 */
abstract class Observer implements Interfaces\Observer
{
    /**
     * Attaches an event.
     *
     * @param Core\Base\Model $object   Processed object.
     * @param string          $event    Event name.
     * @param string          $callback Callback function name to execute.
     *
     * @static
     * @access public
     * @throws \InvalidArgumentException When the provided callback was not a valid callable.
     *
     * @return void
     */
    public static function on(Core\Base\Model $object, $event, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('The provided callback was not a valid callable.');
        }

        if (!isset($object->listeners[$event])) {
            $object->listeners[$event] = array();
        }

        $object->listeners[$event][] = $callback;
    }

    /**
     * Removes an event.
     *
     * @param Core\Base\Model $object   Processed object.
     * @param string          $event    Event name.
     * @param string          $callback Callback function name to execute.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function detach(Core\Base\Model $object, $event, $callback)
    {
        if (isset($object->listeners[$event])) {
            if (false !== ($index = array_search($callback, $object->listeners[$event], true))) {
                unset($object->listeners[$event][$index]);
            }
        }
    }

    /**
     * Fires an event.
     *
     * @param Core\Base\Model $object    Processed object.
     * @param string          $event     Event name.
     * @param array           $arguments Array of arguments.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function fire(Core\Base\Model $object, $event, array $arguments = array())
    {
        $listeners = $object->listeners($event);

        foreach ($listeners as $listener) {
            call_user_func_array($listener, $arguments);
        }
    }
}
