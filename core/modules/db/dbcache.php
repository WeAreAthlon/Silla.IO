<?php
/**
 * DbCache Class.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB;

use Core;
use Core\Modules\Cache\Cache;
use Core\Modules\DB\Interfaces\Adapter;

/**
 * DbCache Class Definition.
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
     * Data source name.
     *
     * @var array
     * @access private
     */
    private $dsn = array();

    /**
     * Flag to indicate whether the persistent cache is turned on.
     *
     * @var boolean
     */
    private $hasPersistence = false;

    /**
     * Caching storage.
     *
     * @var Core\Modules\Cache\Cache
     */
    private $cacheStorage;

    /**
     * Database storage adapter instance.
     *
     * @var Core\Modules\DB\Interfaces\Adapter
     */
    private $dbAdapter;

    /**
     * Executed queries.
     *
     * @var array
     */
    private $queryLog;

    /**
     * Initialize DB Cache.
     *
     * @param Core\Modules\DB\Interfaces\Adapter $adapter      Database instance.
     * @param array                              $dsn          Database meta data.
     * @param Core\Modules\Cache\Cache           $cacheStorage Caching Storage instance.
     * @param boolean                            $persistence  Flag whether to user persistence storage for the cache.
     *
     * @uses   setSchema()
     */
    public function __construct(Adapter $adapter, array $dsn, Cache $cacheStorage, $persistence)
    {
        $this->dsn = $dsn;
        $this->hasPersistence = $persistence;
        $this->cacheStorage = $cacheStorage;
        $this->dbAdapter = $adapter;
    }

    /**
     * Setup caching schemas.
     *
     * @return void
     */
    public function setup()
    {
        $names = $this->dbAdapter->getTables($this->dsn['name']);

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
        return $this->schema[$this->dsn['tables_prefix'] . $table];
    }

    /**
     * Get table meta information from cache or extract it, if missing.
     *
     * @param string $tableName Name of the table.
     *
     * @uses   extractSchemaMeta()
     *
     * @return array
     */
    private function getSchemaMeta($tableName)
    {
        if ($this->hasPersistence) {
            $schemaMeta = $this->cacheStorage->fetch($tableName);
            if (is_null($schemaMeta)) {
                $schemaMeta = $this->extractSchemaMeta($tableName);
                $this->cacheStorage->store($tableName, $schemaMeta);
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
     * @uses   associateType()
     *
     * @return array
     */
    private function extractSchemaMeta($tableName)
    {
        $fields_meta = array();

        $results = $this->dbAdapter->getTableSchema($tableName, $this->dsn['name']);

        foreach ($results as $result) {
            $fields_meta[$result['COLUMN_NAME']]['type'] = $this->associateType($result['DATA_TYPE']);
            $fields_meta[$result['COLUMN_NAME']]['is_null'] = $result['IS_NULLABLE'];
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

    /**
     * Adds a query to the log.
     *
     * @param string $query  Executed query.
     * @param array  $params Query params.
     *
     * @ret
     */
    public function logQuery($query, array $params = array())
    {
        $this->queryLog[] = array('query' => $query, 'params' => $params);
    }
}
