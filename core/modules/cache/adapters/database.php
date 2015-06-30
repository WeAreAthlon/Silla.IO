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
use Core\Modules\DB;

/**
 * Class Database Adapter for caching data.
 */
class Database implements Core\Modules\Cache\Interfaces\Adapter
{
    /**
     * Database table name.
     *
     * @var string
     */
    private $tableName;

    /**
     * Database fields.
     *
     * @var array
     */
    private $fields;

    /**
     * @var Core\Modules\DB\Interfaces\Adapter
     */
    private $db;

    /**
     * Database adapter constructor.
     *
     * @param array $settings Configuration settings.
     */
    public function __construct(array $settings)
    {
        $this->tableName = $settings['database']['table_name'];
        $this->fields = $settings['database']['fields'];
    }

    public function setDatabaseStorage(Core\Modules\DB\Interfaces\Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Stores value by key.
     *
     * @param string  $key    Cache key.
     * @param mixed   $value  Cache value.
     * @param integer $expire Expire time, in seconds(optional).
     *
     * @uses   Core\Modules\DB\Query
     *
     * @return boolean
     */
    public function store($key, $value, $expire = 0)
    {
        if ($expire) {
            $expire = time() + $expire;
        }
        $value = json_encode($value);

        $query = new DB\Query($this->db);

        $exists = $query
            ->select('all')
            ->from($this->tableName)
            ->where("{$this->fields[0]} = ?", array(md5($key)))
            ->first();

        if (is_null($exists)) {
            $insert = $query
                ->insert($this->fields, array(md5($key), $value, $expire))
                ->into($this->tableName);
            return $this->db->run($insert);
        } else {
            $update = $query
                ->update($this->tableName)
                ->set($this->fields, array(md5($key), $value, $expire))
                ->where("{$this->fields[0]} = ?", array(md5($key)));
            return $this->db->run($update);
        }
    }

    /**
     * Fetches stored value by key.
     *
     * @param string $key Variable key.
     *
     * @uses   Core\Modules\DB\Query
     *
     * @return mixed
     */
    public function fetch($key)
    {
        $query = new DB\Query($this->db);

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

        return json_decode($value, true);
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
        $query = new DB\Query($this->db);

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
        $query = new DB\Query($this->db);
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
