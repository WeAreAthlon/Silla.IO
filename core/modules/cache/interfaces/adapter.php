<?php
/**
 * Cache Adapter Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\Cache\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Modules\Cache\Interfaces;

/**
 * Cache Adapter Interface.
 */
interface Adapter
{
    /**
     * Stores value by key.
     *
     * @param string  $key    Cache key.
     * @param mixed   $value  Cache value.
     * @param integer $expire Expire time, in seconds(optional).
     *
     * @return boolean
     */
    public function store($key, $value, $expire = 0);

    /**
     * Fetches stored value by key.
     *
     * @param string $key Cache name.
     *
     * @return mixed
     */
    public function fetch($key);

    /**
     * Checks if a key-value pair exists.
     *
     * @param string $key Cache key.
     *
     * @return boolean
     */
    public function exists($key);

    /**
     * Removes a key-value pair.
     *
     * @param string $key Cache key.
     *
     * @return boolean
     */
    public function remove($key);
}
