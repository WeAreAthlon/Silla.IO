<?php
/**
 * PDO_MYSQL adapter.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Adapters
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Core\Modules\DB\Adapters;

use Core;
use Core\Modules\DB;
use Core\Modules\DB\Interfaces;

/**
 * Database management driver wrapping PDO_MYSQL extension.
 */
class PdoMySql extends \PDO implements Interfaces\Adapter
{
    /**
     * DB Cache instance.
     *
     * @var Core\Modules\DB\DbCache
     */
    public $cache;

    /**
     * Runs a query.
     *
     * @param DB\Query $query Current query object.
     *
     * @return mixed
     */
    public function run(DB\Query $query)
    {
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
     * Formats query object.
     *
     * @param string $sql         SQL query.
     * @param array  $bind_params Parameters.
     *
     * @return mixed
     */
    public function query($sql, array $bind_params = array())
    {
        try {
            if (count($bind_params) > 0) {
                $stmt = $this->prepare($sql);
                $stmt->execute($bind_params);
            } else {
                $stmt = parent::query($sql);
            }

            if (!$stmt->columnCount()) {
                if ($stmt->rowCount()) {
                    return true;
                }

                return false;
            }

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $backtrace = debug_backtrace();
            next($backtrace);
            $callee = next($backtrace);
            trigger_error(
                "SQL '{$sql}'. Occured in {$callee['class']}->{$callee['function']} on {$callee['line']} line.<br />" .
                $e->getMessage(),
                E_USER_ERROR
            );

            exit;
        }
        return $result;
    }

    /**
     * Executes a query.
     *
     * @param string $sql         SQL query string.
     * @param array  $bind_params Query parameters.
     *
     * @return mixed
     */
    public function execute($sql, array $bind_params = array())
    {
        try {
            if (count($bind_params) > 0) {
                $stmt = $this->prepare($sql);
                $result = $stmt->execute($bind_params);
            } else {
                $result = $this->exec($sql);
            }
        } catch (\PDOException $e) {
            $backtrace = debug_backtrace();
            next($backtrace);
            $callee = next($backtrace);
            trigger_error(
                "SQL '{$sql}'. Occured in {$callee['class']}->{$callee['function']} on {$callee['line']} line.<br />" .
                $e->getMessage(),
                E_USER_ERROR
            );
            exit;
        }

        return $result;
    }

    /**
     * Sets connection charset.
     *
     * @param string $charset Character set type.
     *
     * @return integer
     */
    public function setCharset($charset = 'utf8')
    {
        $sql = 'SET CHARACTER SET ' . $charset;
        $this->storeQueries($sql);

        return $this->exec($sql);
    }

    /**
     * Get all tables under the database.
     *
     * @param string $schema Schema contents.
     *
     * @return array
     */
    public function getTables($schema)
    {
        $sql = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?';
        $this->storeQueries($sql, array($schema));
        $stmt = $this->prepare($sql);
        $stmt->execute(array($schema));

        return Core\Utils::arrayFlatten($stmt->fetchAll(\PDO::FETCH_NUM));
    }

    /**
     * Retrieves table schema.
     *
     * @param string $table  Table name.
     * @param string $schema Schema contents.
     *
     * @return array
     */
    public function getTableSchema($table, $schema)
    {
        $query = 'SELECT COLUMN_NAME,
                      DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_nullABLE, EXTRA, COLUMN_DEFAULT, COLUMN_KEY, COLUMN_TYPE
                  FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE table_name = ? AND table_schema = ?';
        $this->storeQueries($query, array($table, $schema));

        $stmt = $this->prepare($query);
        $stmt->execute(array($table, $schema));

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Caches queries.
     *
     * @param string $query  SQL Query.
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
     * Returns the last insert id representation.
     *
     * @return integer
     */
    public function getLastInsertId()
    {
        return \PDO::lastInsertId();
    }

    /**
     * Sanitizes string.
     *
     * @param string $value Value to be escaped.
     *
     * @return string
     */
    public function escapeString($value)
    {
        return \PDO::quote($value);
    }

    /**
     * Purge cache.
     *
     * @param string $table Table name.
     *
     * @return mixed
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
     * Builds SQL part of the query.
     *
     * @param string $query Query string.
     *
     * @throws \DomainException DB Adapter does not support the required JOIN type.
     *
     * @return string
     */
    private function buildSql($query)
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
                        throw new \DomainException('DB Adapter does not support the JOIN type:' . $join['type']);
                    }

                    $sql[] = $join['type'];
                    $sql[] = 'JOIN';
                    $sql[] = $join['table'];

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
        } elseif ($query->type === 'create_table') {
            $sql[] = 'CREATE TABLE IF NOT EXISTS';
            $sql[] = $query->table;
            $fields = array();

            foreach ($query->db_fields as $field => $attributes) {
                $is_primary_key = false;
                $attrs = $this->convertAttributes($attributes);

                if ($pos = array_search('pk', $attrs)) {
                    unset($attrs[$pos]);
                    $is_primary_key = true;
                }

                $fields[] = $field . ' ' . implode(' ', $attrs);

                if ($is_primary_key) {
                    $fields[] = 'PRIMARY KEY(' . $field . ')';
                }
            }

            $sql[] = '(' . implode(',', $fields) . ')';
            $sql[] = 'ENGINE ' . $query->table_engine;
        } elseif ($query->type === 'drop_table') {
            $sql[] = 'DROP TABLE ' . $query->table;
        } elseif ($query->type === 'add_columns') {
            $sql[] = 'ALTER TABLE';
            $sql[] = $query->table;
            $sql[] = 'ADD COLUMN';
            $fields = array();

            foreach ($query->db_fields as $field => $attributes) {
                $is_primary_key = false;
                $attrs = $this->convertAttributes($attributes);

                if ($pos = array_search('pk', $attrs)) {
                    unset($attrs[$pos]);
                    $is_primary_key = true;
                }

                $fields[] = $field . ' ' . implode(' ', $attrs);

                if ($is_primary_key) {
                    $fields[] = 'PRIMARY KEY(' . $field . ')';
                }
            }

            $sql[] = '(' . implode(',', $fields) . ')';
        } elseif ($query->type === 'drop_columns') {
            $sql[] = 'ALTER TABLE';
            $sql[] = $query->table;

            $cols = array();

            foreach ($query->db_fields as $column) {
                $cols[] = 'DROP COLUMN ' . $column;
            }

            $sql[] = implode(',', $cols);
        }

        return implode(' ', $sql);
    }

    /**
     * Convert attributes method to adapter locals.
     *
     * @param array $attributes Attributes to be converted.
     *
     * @return array
     */
    private function convertAttributes(array $attributes)
    {
        $result = array();
        foreach ($attributes as $key => $value) {
            switch ($key) {
                case 'type':
                    $type = $value;

                    if ($value === 'string') {
                        $type = 'VARCHAR';
                    }

                    if (isset($attributes['length'])) {
                        $type .= '(' . $attributes['length'] . ')';
                    }

                    $result[] = $type;
                    break;

                case 'not_null':
                    $result[] = 'NOT null';
                    break;

                case 'unsigned':
                    $result[] = 'UNSIGNED';
                    break;

                case 'ai':
                    $result[] = 'AUTO_INCREMENT';
                    break;

                case 'pk':
                    $result[] = 'pk';
                    break;
            }
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
