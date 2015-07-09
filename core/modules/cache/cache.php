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
use Core\Modules\DB;

/**
 * Wrapper for caching data.
 */
final class Cache
{
    /**
     * Reference to the current adapter of the Cache object.
     *
     * @var Core\Modules\Cache\Interfaces\Adapter
     */
    private $adapter = null;

    /**
     * Cache constructor.
     *
     * @param string $adapter  Cache Adapter.
     * @param array  $settings Configuration settings.
     *
     * @throws \DomainException          Not supported Cache adapter.
     * @throws \InvalidArgumentException Not compatible Cache adapter type.
     */
    public function __construct($adapter, array $settings)
    {
        if (!class_exists($adapter)) {
            throw new \DomainException('Not supported Cache adapter type: ' . $adapter);
        }

        if (!is_subclass_of($adapter, 'Core\Modules\Cache\Interfaces\Adapter')) {
            throw new \InvalidArgumentException('Not compatible Cache adapter type: ' . $adapter);
        }

        $this->adapter = new $adapter($settings);
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

    /**
     * Retrieve the current adapter instance.
     *
     * @return Interfaces\Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
