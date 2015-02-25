<?php
/**
 * Redis Cache Adapter.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Cache\Adapters
 * @author     Aleksandar Krastev <aleksandar.krastev@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Cache\Adapters;

use Core;
use Predis;

/**
 * Class Redis Adapter for caching data.
 */
class Redis implements Core\Modules\Cache\Interfaces\Adapter
{
    /**
     * Redis client.
     *
     * @var string
     * @access private
     */
    private $redisClient;

    /**
     * Redis constructor.
     *
     * @access public
     * @uses Predis\Client;
     */
    public function __construct()
    {
        $connParams = Core\Config()->CACHE['redis'];
        $this->redisClient = new Predis\Client($connParams);
        $this->redisClient->connect();
    }

    /**
     * Stores value by key.
     *
     * @param string  $key    Cache key.
     * @param mixed   $value  Cache value.
     * @param integer $expire Expire time, in seconds(optional).
     *
     * @access public
     * @uses \Predis\Response
     *
     * @return boolean
     */
    public function store($key, $value, $expire = 0)
    {
        $value = json_encode($value);

        if ($expire) {
            $status = $this->redisClient->set($key, $value, 'EX', $expire);
        } else {
            $status = $this->redisClient->set($key, $value);
        }

        return true;
    }

    /**
     * Fetches stored value by key.
     *
     * @param string $key Cache key.
     *
     * @access public
     * @uses Predis\Client;
     *
     * @return mixed
     */
    public function fetch($key)
    {
        $response = $this->redisClient->get($key);

        return json_decode($response);
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
        return $this->redisClient->exists($key);
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
        return $this->redisClient->del($key);
    }
}
