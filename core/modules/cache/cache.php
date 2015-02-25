<?php
/**
 * Cache Management.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Cache
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Cache;

use Core;

/**
 * Wrapper for caching data.
 */
final class Cache
{
    /**
     * Reference to the current instance of the Cache object.
     *
     * @var Cache
     * @access private
     * @static
     */
    private static $instance = null;

    /**
     * Reference to the current adapter of the Cache object.
     *
     * @var mixed
     * @access private
     */
    private $adapter = null;

    /**
     * Cache constructor.
     *
     * @param string $adapter Cache Adapter.
     *
     * @access private
     * @throws \DomainException          Not supported Cache adapter.
     * @throws \InvalidArgumentException Not compatible Cache adapter type.
     */
    private function __construct($adapter)
    {
        if (!class_exists($adapter)) {
            throw new \DomainException('Not supported Cache adapter type: ' . $adapter);
        }

        if (!is_subclass_of($adapter, 'Core\Modules\Cache\Interfaces\Adapter')) {
            throw new \InvalidArgumentException('Not compatible Cache adapter type: ' . $adapter);
        }

        $this->adapter = new $adapter;
    }

    /**
     * Cloning of Cache is disallowed.
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(__CLASS__ . ' cannot be cloned! It is a singleton.', E_USER_ERROR);
    }

    /**
     * Returns an instance of the Cache object.
     *
     * @param string $adapter Adapter name.
     *
     * @access public
     * @static
     * @final
     * @uses   Core\Registry()
     *
     * @return Cache
     */
    final public static function getInstance($adapter)
    {
        if (null === self::$instance) {
            $adapter = 'Core\Modules\Cache\Adapters\\' . $adapter;

            self::$instance = new Cache($adapter);

            Core\Registry()->set('cache', self::$instance);
        }

        return self::$instance;
    }

    /**
     * Stores value by key.
     *
     * @param string  $key    Cache key.
     * @param mixed   $value  Cache value.
     * @param integer $expire Expire time, in seconds(optional).
     *
     * @return boolean
     */
    public function store($key, $value, $expire = 0)
    {
        if ($expire) {
            return $this->adapter->store($key, $value, $expire);
        }

        return $this->adapter->store($key, $value);
    }

    /**
     * Fetches stored value by key.
     *
     * @param string $key Cache key.
     *
     * @return mixed
     */
    public function fetch($key)
    {
        return $this->adapter->fetch($key);
    }

    /**
     * Checks if a key-value pair exists.
     *
     * @param string $key Cache key.
     *
     * @return boolean
     */
    public function exists($key)
    {
        return $this->adapter->exists($key);
    }

    /**
     * Removes a key-value pair.
     *
     * @param string $key Cache key.
     *
     * @return boolean
     */
    public function remove($key)
    {
        return $this->adapter->remove($key);
    }
}
