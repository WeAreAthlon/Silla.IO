<?php
/**
 * Cache Adapter Interface.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Cache\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
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
