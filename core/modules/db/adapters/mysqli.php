<?php
/**
 * MySQLi adapter.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Adapters
 * @author     Plamen Nikolov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Adapters;

use Core;
use Core\Modules\DB;
use Core\Modules\DB\Interfaces;

/**
 * Database management driver wrapping mysqli extension.
 */
class MySQLi implements Interfaces\Adapter
{
    /**
     * DB Cache instance.
     *
     * @var Core\Modules\DB\DbCache
     */
    public $cache;

    /**
     * MySQLi object. Represents a connection between PHP and a MySQL database.
     *
     * @var \mysqli
     */
    protected $link;

    /**
     * Init actions.
     *
     * @param string $host     Host of the storage engine.
     * @param string $database Name of the database.
     * @param string $username Username to access the database.
     * @param string $password Password phrase for the user.
     */
    public function __construct($host, $database, $username, $password)
    {
        $this->link = new \MySQLi($host, $username, $password, $database);
    }

    /**
     * Run a query.
     *
     * @param DB\Query $query Current query object.
     *
     * @return array|boolean
     */
    public function run(DB\Query $query)
    {
        $query->appendTablesPrefix(Core\Config()->DB['tables_prefix']);
        $sql = $this->buildSql($query);
        $query_hash = md5(serialize(array('query' => $sql, 'bind_params' => $query->bind_params)));

        $query_cache_name = implode(',', array($query->table, implode(',', array_map(function ($item) {
            return $item['table'];
        }, $query->join))));

        if (array_key_exists($query_hash, Core\DbCache()->getCache($query_cache_name))) {
            return Core\DbCache()->getCache($query_cache_name, $query_hash);
        }

        $this->storeQueries($sql, $query->bind_params);

        if ($query->type === 'select') {
            $res = $this->query($sql, $query->bind_params);
            Core\DbCache()->setCache($query_cache_name, $query_hash, $res);

            return $res;
        }

        foreach (Core\DbCache()->cache as $table => $value) {
            if (in_array($query->table, explode(',', $table), true)) {
                Core\DbCache()->clearCache($table);
            }
        }

        return $this->execute($sql, $query->bind_params);
    }

    /**
     * Formats a query.
     *
     * @param string $sql         SQL string query.
     * @param array  $bind_params Array of parameter values.
     *
     * @return array|boolean
     */
    public function query($sql, array $bind_params = array())
    {
        $resource = false;
        if (count($bind_params) > 0) {
            $stmt = $this->prepare($sql, $bind_params);

            if ($stmt) {
                $stmt->execute();
                $resource = $stmt->get_result();
                $stmt->close();
            }
        } else {
            $resource = $this->link->query($sql);
        }

        if (!is_object($resource)) {
            return !!$this->link->affected_rows;
        }

        return $this->fetchAll($resource);
    }

    /**
     * Executes a query.
     *
     * @param string $sql         SQL string query.
     * @param array  $bind_params Parameter to bind.
     *
     * @return boolean
     */
    public function execute($sql, array $bind_params = array())
    {
        if (count($bind_params) > 0) {
            $stmt = $this->prepare($sql, $bind_params);
            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $result = $this->link->query($sql);
        }

        return empty($this->link->error) || $result;
    }

    /**
     * Sets connection charset.
     *
     * @param string $charset Charset type.
     *
     * @return boolean
     */
    public function setCharset($charset = 'utf8')
    {
        return $this->execute('SET CHARACTER SET ' . $charset);
    }

    /**
     * Retrieve all tables.
     *
     * @param string $schema Schema definition.
     *
     * @return array
     */
    public function getTables($schema)
    {
        return Core\Utils::arrayFlatten(
            $this->query('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', array($schema))
        );
    }

    /**
     * Retrieves a table schema.
     *
     * @param string $table  Table name.
     * @param string $schema Schema definition.
     *
     * @return array
     */
    public function getTableSchema($table, $schema)
    {
        return $this->query(
            "SELECT COLUMN_NAME,
                DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, EXTRA, COLUMN_DEFAULT, COLUMN_KEY, COLUMN_TYPE
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?",
            array($table, $schema)
        );
    }

    /**
     * Stores a query in the cache.
     *
     * @param string $query  Query string.
     * @param array  $params Query parameters.
     *
     * @return void
     */
    public function storeQueries($query, array $params = array())
    {
        if (empty($params)) {
            DB\DB::$queries[] = $query;
        } else {
            DB\DB::$queries[] = array('query' => $query, 'params' => $params);
        }
    }

    /**
     * Retrieves last inserted id.
     *
     * @return integer
     */
    public function getLastInsertId()
    {
        return $this->link->insert_id;
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
        return $this->link->escape_string($value);
    }

    /**
     * Purges cache.
     *
     * @param string $table Table name.
     *
     * @return boolean
     */
    public function clearTable($table)
    {
        foreach (Core\DbCache()->cache as $tbl => $value) {
            if (in_array($table, explode(',', $tbl), true)) {
                Core\DbCache()->clearCache($tbl);
            }
        }

        return $this->execute("TRUNCATE TABLE {$table}");
    }

    /**
     * Builds a sql query.
     *
     * @param DB\Query $query SQL Query.
     *
     * @throws \DomainException DB Adapter does not support the required JOIN type.
     *
     * @return string
     */
    protected function buildSql(DB\Query $query)
    {
        $sql = array();

        if ($query->type === 'select') {
            $sql[] = 'SELECT';
            $sql[] = ($query->db_fields === 'all') ? '*' :
                (is_array($query->db_fields) ? implode(',', $query->db_fields) : $query->db_fields);
            $sql[] = 'FROM';
            $sql[] = $query->table;

            if ($query->join) {
                foreach ($query->join as $join) {
                    if (!in_array($join['type'], self::getSupportedJoinTypes(), true)) {
                        throw new \DomainException('DB Adapter not supporting the required JOIN type:' . $join['type']);
                    }

                    $sql[] = $join['type'];
                    $sql[] = 'JOIN';
                    $sql[] = Core\Config()->DB['tables_prefix'] . $join['table'];

                    if ($join['condition']) {
                        $sql[] = 'ON (' . $join['condition'] . ')';
                    }
                }
            }

            if ($query->where) {
                $sql[] = 'WHERE';
                $sql[] = implode(' AND ', array_map(function ($item) {
                    return '(' . $item . ')';
                }, $query->where));
            }

            if ($query->order) {
                $sql[] = 'ORDER BY';
                $sql[] = implode(', ', array_map(function ($item) {
                    return "{$item['field']} {$item['direction']}";
                }, $query->order));
            }

            if ($query->limit) {
                $sql[] = 'LIMIT';
                $sql[] = $query->limit;

                if ($query->offset) {
                    $sql[] = 'OFFSET';
                    $sql[] = $query->offset;
                }
            }
        } elseif ($query->type === 'insert') {
            $sql[] = 'INSERT IGNORE INTO';
            $sql[] = $query->table;
            $sql[] = '(' . implode(',', $query->db_fields) . ')';
            $sql[] = 'VALUES';

            if (isset($query->bind_params[0]) && is_array($query->bind_params[0])) {
                $sql[] = implode(',', array_map(function ($item) {
                    return '(' . implode(',', array_map(function () {
                        return '?';
                    }, $item)) . ')';
                }, $query->bind_params));
                $query->bind_params = Core\Utils::arrayFlatten($query->bind_params);
            } else {
                $sql[] = '(' . implode(',', array_map(function () {
                    return '?';
                }, $query->bind_params)) . ')';
            }
        } elseif ($query->type === 'update') {
            $sql[] = 'UPDATE';
            $sql[] = $query->table;
            $sql[] = 'SET';
            $sql[] = implode(',', array_map(function ($item) {
                return $item . ' = ?';
            }, $query->db_fields));
            $sql[] = 'WHERE';
            $sql[] = implode(' AND ', array_map(function ($item) {
                return '(' . $item . ')';
            }, $query->where));
        } elseif ($query->type === 'remove') {
            $sql[] = 'DELETE FROM';
            $sql[] = $query->table;
            $sql[] = 'WHERE';
            $sql[] = implode(' AND ', array_map(function ($item) {
                return '(' . $item . ')';
            }, $query->where));
        }

        return implode(' ', $sql);
    }

    /**
     * Prepares a query for execution.
     *
     * @param string $sql         SQL Query.
     * @param array  $bind_params Parameters.
     *
     * @throws \InvalidArgumentException The number of values passed and placeholders mismatch.
     * @throws \LogicException           Error in the prepared statement.
     *
     * @return \mysqli_stmt|false
     */
    private function prepare($sql, array $bind_params = array())
    {
        if (substr_count($sql, '?') !== count($bind_params)) {
            throw new \InvalidArgumentException('The number of values passed and placeholders mismatch');
        }

        if ($stmt = $this->link->prepare($sql)) {
            $reflection = new \ReflectionClass('mysqli_stmt');
            $method = $reflection->getMethod('bind_param');

            $param_types = array_reduce($bind_params, function ($carry) {
                $carry .= 's';
                return $carry;
            });

            array_unshift($bind_params, $param_types);
            $method->invokeArgs($stmt, Core\Utils::arrayToRefValues($bind_params));
        } else {
            throw new \LogicException($this->link->error);
        }

        return $stmt;
    }

    /**
     * Fetch all rows satisfying the query.
     *
     * @param \mysqli_result $resource Resource source.
     *
     * @return array
     */
    private function fetchAll(\mysqli_result $resource)
    {
        $result = array();

        while ($row = $resource->fetch_assoc()) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Retrieves all supported types of SQL JOIN operator.
     *
     * @static
     *
     * @return array
     */
    public static function getSupportedJoinTypes()
    {
        return array(
            'LEFT', 'RIGHT', 'INNER', 'OUTER', 'CROSS', 'LEFT OUTER', 'RIGHT OUTER', 'NATURAL', 'STRAIGHT_JOIN'
        );
    }
}
