<?php
/**
 * DbCache Class.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB;

use Core;

/**
 * DbCache Class Definition. Singleton class for handling the database cache.
 */
final class DbCache
{
    /**
     * Whether there is a schema or not.
     *
     * @var boolean
     * @access private
     */
    private $hasSchema = false;

    /**
     * Stores the queries cache.
     *
     * @var array
     * @access private
     */
    public $cache = array();

    /**
     * Stores tables schema.
     *
     * @var array
     * @access private
     */
    private $schema = array();

    /**
     * Stores the state of the tables(if the table has been changed, after cached query).
     *
     * @var array
     * @access private
     */
    private $tables = array();

    /**
     * Stores the prepared database statements.
     *
     * @var array
     * @access private
     */
    private $statements = array();

    /**
     * Initialize DB Cache.
     *
     * @uses   setSchema()
     */
    public function __construct()
    {
        $names = Core\DB()->getTables(Core\Config()->DB['name']);

        if (empty($names)) {
            return;
        }

        foreach ($names as $tableName) {
            $schemaMeta = $this->getSchemaMeta($tableName);
            /* Cache the schema for the current execution of the script */
            $this->setSchema($tableName, $schemaMeta);
        }

        $this->hasSchema = true;
    }

    /**
     * Cloning of DbCache is disallowed.
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
     * Set method.
     *
     * @param string $attr Attribute name.
     * @param mixed  $val  Attribute value.
     *
     * @return void
     */
    public function __set($attr, $val)
    {
        $this->{$attr} = $val;
    }

    /**
     * Get method.
     *
     * @param string $attr Attribute name.
     *
     * @return mixed
     */
    public function __get($attr)
    {
        return $this->{$attr};
    }

    /**
     * Cache a query and its result.
     *
     * @param string $table  Table name.
     * @param string $query  SQL query.
     * @param array  $result Result array.
     *
     * @access public
     *
     * @return void
     */
    public function setCache($table, $query, array $result)
    {
        if (!$this->cache[$table]) {
            $this->cache[$table] = array();
        }

        $this->cache[$table][$query] = $result;
    }

    /**
     * Get the result of a cached query, or return the whole cache.
     *
     * @param string $table Table name.
     * @param string $query Query string(optional).
     *
     * @access public
     *
     * @return array
     */
    public function getCache($table, $query = null)
    {
        if ($query) {
            return $this->cache[$table][$query];
        }

        if (!(isset($this->cache[$table]) && $this->cache[$table])) {
            $this->cache[$table] = array();
        }

        return $this->cache[$table];
    }

    /**
     * Clears the cache for a given table.
     *
     * @param string $table Table name.
     *
     * @return void
     */
    public function clearCache($table)
    {
        unset($this->cache[$table]);
    }

    /**
     * Set the state of a table.
     *
     * @param string  $name       Name of the table.
     * @param boolean $is_changed Whether the schema has been changed.
     *
     * @access private
     *
     * @return void
     */
    private function setTable($name, $is_changed)
    {
        $this->tables[$name] = $is_changed;
    }

    /**
     * Get the state of a table.
     *
     * @param string $name Name of the table.
     *
     * @access public
     *
     * @return boolean
     */
    public function getTable($name)
    {
        return $this->tables[$name];
    }

    /**
     * Cache a database statement.
     *
     * @param string $query     It's used for a key.
     * @param mixed  $statement Statement to cache.
     *
     * @access private
     *
     * @return void
     */
    private function setStatement($query, $statement)
    {
        $this->statements[$query] = $statement;
    }

    /**
     * Get a database statement.
     *
     * @param string $query It's used for a key.
     *
     * @access public
     *
     * @return object
     */
    public function getStatement($query)
    {
        return $this->statements[$query];
    }

    /**
     * Set a table schema.
     *
     * @param string $table Name of the table.
     * @param array  $value Schema data.
     *
     * @access private
     *
     * @return void
     */
    private function setSchema($table, array $value)
    {
        $this->schema[$table] = $value;
    }

    /**
     * Get the table schema.
     *
     * @param string $table Name of the table.
     *
     * @access public
     *
     * @return array
     */
    public function getSchema($table)
    {
        return $this->schema[$table];
    }

    /**
     * Get table meta information from cache or extract it, if missing.
     *
     * @param string $tableName Name of the table.
     *
     * @uses   Core\Cache()
     * @uses   getAllTableNames()
     * @uses   extractSchemaMeta()
     *
     * @return array
     */
    private function getSchemaMeta($tableName)
    {
        if (Core\Config()->CACHE['db_schema']) {
            $schemaMeta = Core\Cache()->fetch($tableName);
            if (is_null($schemaMeta)) {
                $schemaMeta = $this->extractSchemaMeta($tableName);
                Core\Cache()->store($tableName, $schemaMeta);
            }
        } else {
            $schemaMeta = $this->extractSchemaMeta($tableName);
        }

        return $schemaMeta;
    }

    /**
     * Retrieve meta information about supplied database table.
     *
     * @param string $tableName Name of the table.
     *
     * @throws \LogicException Error extracting schema meta information.
     * @access private
     * @uses   Core\DB()
     * @uses   associateType()
     *
     * @return array
     */
    private function extractSchemaMeta($tableName)
    {
        $fields_meta = array();

        $results = Core\DB()->getTableSchema($tableName, Core\Config()->DB['name']);

        foreach ($results as $result) {
            $fields_meta[$result['COLUMN_NAME']]['type'] = $this->associateType($result['DATA_TYPE']);
            $fields_meta[$result['COLUMN_NAME']]['is_null'] = $result['IS_nullABLE'];
            $fields_meta[$result['COLUMN_NAME']]['extra'] = $result['EXTRA'];
            $fields_meta[$result['COLUMN_NAME']]['default'] = $result['COLUMN_DEFAULT'];
            $fields_meta[$result['COLUMN_NAME']]['unique'] = $result['COLUMN_KEY'] == 'UNI';

            /* If the field is string, varchar, text etc. add the max length of this field into the schema */
            if ('string' === $fields_meta[$result['COLUMN_NAME']]['type']) {
                $fields_meta[$result['COLUMN_NAME']]['length'] = $result['CHARACTER_MAXIMUM_LENGTH'];
            }

            if ('enum' === $fields_meta[$result['COLUMN_NAME']]['type'] && isset($result['COLUMN_TYPE'])) {
                $fields_meta[$result['COLUMN_NAME']]['values'] = explode(
                    "','",
                    str_replace(array("enum('", "')", "''"), array('', '', "'"), $result['COLUMN_TYPE'])
                );
            }
        }

        if (empty($fields_meta)) {
            throw new \LogicException('Error extracting schema meta information.');
        }

        return $fields_meta;
    }

    /**
     * Associate the field type with specific value used in the queries.
     *
     * @param string $type Field type.
     *
     * @access private
     *
     * @return string
     */
    private function associateType($type)
    {
        switch ($type) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                return 'int';
            case 'double':
            case 'float':
            case 'decimal':
                return 'double';
            case 'time':
            case 'date':
            case 'timestamp':
                return 'date';
            case 'datetime':
                return 'datetime';
            case 'enum':
                return 'enum';
            default:
                return 'string';
        }
    }
}
