<?php
/**
 * Database Cache Adapter.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Cache\Adapters
 * @author     Aleksandar Krastev <aleksandar.krastev@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Cache\Adapters;

use Core;
use Core\Modules\DB\Query;

/**
 * Class Database Adapter for caching data.
 */
class Database implements Core\Modules\Cache\Interfaces\Adapter
{
    /**
     * Database table name.
     *
     * @var string
     * @access private
     */
    private $tableName;

    /**
     * Database fields.
     *
     * @var array
     * @access private
     */
    private $fields;

    /**
     * Database constructor.
     *
     * @access public
     */
    public function __construct()
    {
        $this->tableName = Core\Config()->CACHE['database']['table_name'];
        $this->fields    = Core\Config()->CACHE['database']['fields'];
    }

    /**
     * Stores value by key.
     *
     * @param string  $key    Cache key.
     * @param mixed   $value  Cache value.
     * @param integer $expire Expire time, in seconds(optional).
     *
     * @access public
     * @uses   Core\Modules\DB\Query
     *
     * @return boolean
     */
    public function store($key, $value, $expire = 0)
    {
        if ($expire) {
            $expire = time() + $expire;
        }
        $value = serialize($value);

        $query  = new Query();
        $exists = $query
            ->select('all')
            ->from($this->tableName)
            ->where("{$this->fields[0]} = ?", array(md5($key)))
            ->first();

        if (is_null($exists)) {
            $insert = $query
                ->insert($this->fields, array(md5($key), $value, $expire))
                ->into($this->tableName);

            return Core\DB()->run($insert);
        } else {
            $update = $query
                ->update($this->tableName)
                ->set($this->fields, array(md5($key), $value, $expire))
                ->where("{$this->fields[0]} = ?", array(md5($key)));

            return Core\DB()->run($update);
        }
    }

    /**
     * Fetches stored value by key.
     *
     * @param string $key Variable key.
     *
     * @access public
     * @uses   Core\Modules\DB\Query
     *
     * @return mixed
     */
    public function fetch($key)
    {
        $query = new Query();
        $query = $query
            ->select('all')
            ->from($this->tableName)
            ->where("{$this->fields[0]} = ?", array(md5($key)));

        $result = $query->first();

        if (is_null($result)) {
            return null;
        }

        list(, $value, $expire) = array_values($result);

        if ($expire && $expire < time()) {
            $query->remove()->run();

            return null;
        }

        return unserialize($value);
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
        $query  = new Query();
        $exists = $query
            ->select('all')
            ->from($this->tableName)
            ->where("{$this->fields[0]} = ?", array(md5($key)))
            ->exists();

        return $exists;
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
        $query = new Query();
        $query = $query
            ->select('all')
            ->from($this->tableName)
            ->where("{$this->fields[0]} = ?", array(md5($key)));

        if ($query->exists()) {
            $query->remove()->run();

            return true;
        } else {
            return false;
        }
    }
}
