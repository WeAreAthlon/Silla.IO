<?php
/**
 * MySQL adapter.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Adapters
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Adapters;

use Core;
use Core\Modules\DB;
use Core\Modules\DB\Interfaces;

/**
 * Database management driver wrapping mysql extension.
 */
class MySQL extends MySQLi implements Interfaces\Adapter
{
    /**
     * DB Cache instance.
     *
     * @var Core\Modules\DB\DbCache
     */
    public $cache;

    /**
     * Init actions.
     *
     * @param string $host     Host of the storage engine.
     * @param string $database Name of the database.
     * @param string $username Username to access the database.
     * @param string $password Password for the user.
     */
    public function __construct($host, $database, $username, $password)
    {
        mysql_connect($host, $username, $password);
        mysql_select_db($database);
    }

    /**
     * Formats a query.
     *
     * @param string $sql         SQL string query.
     * @param array  $bind_params Array of parameter values.
     *
     * @return array|resource|boolean
     */
    public function query($sql, array $bind_params = array())
    {
        if (count($bind_params) > 0) {
            $stmt     = $this->prepare($sql, $bind_params);
            $resource = mysql_query($stmt);
        } else {
            $resource = mysql_query($sql);
        }

        if (gettype($resource) != 'resource') {
            if (mysql_affected_rows()) {
                return true;
            }

            return false;
        }

        return $this->fetchAll($resource);
    }

    /**
     * Executes a query.
     *
     * @param string $sql         SQL string query.
     * @param array  $bind_params Parameter to bind.
     *
     * @return resource
     */
    public function execute($sql, array $bind_params = array())
    {
        if (count($bind_params) > 0) {
            $stmt   = $this->prepare($sql, $bind_params);
            $result = mysql_query($stmt);
        } else {
            $result = mysql_query($sql);
        }

        return $result;
    }

    /**
     * Retrieves last inserted id.
     *
     * @return integer
     */
    public function getLastInsertId()
    {
        $result = $this->query('SELECT LAST_INSERT_ID()');

        return $result[0][0];
    }

    /**
     * Sanitizes string.
     *
     * @param string $value Variable to be escaped.
     *
     * @return string
     */
    public function escapeString($value)
    {
        return mysql_real_escape_string($value);
    }

    /**
     * Prepares a query for execution.
     *
     * @param string $sql         SQL Query.
     * @param array  $bind_params Parameters.
     *
     * @throws \Exception The number of values passed and placeholders mismatch.
     *
     * @return mixed
     */
    private function prepare($sql, array $bind_params = array())
    {
        if (substr_count($sql, '?') !== count($bind_params)) {
            throw new \Exception('The number of values passed and placeholders mismatch');
        }

        $keys   = array();
        $values = array();

        foreach ($bind_params as $value) {
            $keys[]   = '/\{\{param' . count($values) . '\}\}/';
            $sql      = preg_replace('/\?/', '{{param' . count($values) . '}}', $sql, 1);
            $values[] = '"' . mysql_real_escape_string($value) . '"';
        }
        $res = preg_replace($keys, $values, $sql, 1);

        return $res;
    }

    /**
     * Fetch all rows satisfying the query.
     *
     * @param resource $resource Resource source.
     *
     * @return array
     */
    private function fetchAll($resource)
    {
        $result = array();

        while ($row = mysql_fetch_assoc($resource)) {
            $result[] = $row;
        }

        return $result;
    }
}
