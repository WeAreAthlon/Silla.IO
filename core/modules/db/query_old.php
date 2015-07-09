<?php
/**
 * Class Query.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB;

/**
 * Class Query definition.
 */
class Query extends \Zob\Query
{
    /**
     * Reflection object container.
     *
     * @var \ReflectionClass
     * @static
     */
    public static $reflection = null;

    /**
     * Records per pagination page.
     *
     * @var integer
     * @access public
     */
    public $perPage = null;

    /**
     * Current records pagination page.
     *
     * @var integer
     * @access public
     */
    public $page = null;

    /**
     * Items array.
     *
     * @var array
     * @access private
     */
    private $items = array();

    /**
     * Object class name.
     *
     * @var string
     * @access private
     */
    private $objectsClass = null;

    /**
     * Query options array.
     *
     * @var array
     * @access private
     */
    private $queryOptions = array(
        'bind_params' => array(),
        'where' => array(),
        'table' => null,
        'limit' => null,
        'order' => array(),
        'join' => array(),
        'page' => null,
        'per_page' => null,
    );

    /**
     * Whether tables names prefix has been appended.
     *
     * @var boolean
     */
    private $tablesPrefixAppended = false;

    /**
     * @var Core\Modules\DB\Interfaces\Adapter
     */
    private $db;

    /**
     * Init actions.
     *
     * @param Core\Modules\DB\Interfaces\Adapter $db        Database adapter instance.
     * @param string                             $className Name of the class.
     */
    public function __construct(Core\Modules\DB\Interfaces\Adapter $db, $className = null)
    {
        $this->objectsClass = $className;
        $this->db = $db;

        self::$reflection = self::$reflection ? self::$reflection : new \ReflectionClass('Core\Modules\DB\Query');
    }

    /**
     * Function call dispatcher.
     *
     * @param string $name Name of the function.
     * @param array  $args Array of arguments of the function.
     *
     * @return array
     */
    public function __call($name, array $args)
    {
        if (!self::$reflection->hasMethod($name) ||
            (self::$reflection->hasMethod($name) &&
                self::$reflection->getMethod($name)->getNumberOfParameters() != count($args)
            )
        ) {
            $this->run();

            $res = array();

            foreach ($this as $itm) {
                $res[$itm->{$itm->primaryKeyField}] = call_user_func_array(array($itm, $name), $args);
            }

            if (1 === count($res)) {
                $res = current($res);
            }

            return $res;
        }

        return call_user_func_array(array($this, $name), $args);
    }

    /**
     * Generic getter.
     *
     * @param string $name Name.
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->queryOptions)) {
            return $this->queryOptions[$name];
        }

        if (!self::$reflection->hasProperty($name)) {
            $res = array();
            foreach ($this as $itm) {
                $res[$itm->{$itm->primaryKeyField}] = $itm->$name;
            }

            if (1 === count($res)) {
                $res = current($res);
            }

            return $res;
        }
    }

    /**
     * Generic setter.
     *
     * @param string $name  Name.
     * @param mixed  $value Value.
     *
     * @return void
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->queryOptions)) {
            $this->queryOptions[$name] = $value;
        } elseif (!in_array($name, get_object_vars($this), true)) {
            foreach ($this as $itm) {
                $itm->$name = $value;
            }
        }
    }

    /**
     * Clone method.
     *
     * @return void
     */
    public function __clone()
    {
        $this->items = array();
    }

    /**
     * Run method.
     *
     * @return void
     */
    private function run()
    {
        if (!$this->items) {
            $items = $this->db->run($this);

            if ($this->objectsClass) {
                foreach ($items as $item) {
                    $obj = new $this->objectsClass($item);
                    $this->items[] = $obj;
                }
            } else {
                $this->items = $items;
            }

            if (!is_array($this->items)) {
                $this->items = array($this->items);
            }
        }
    }

    /* ArrayAccess methods */

    /**
     * Sets the offset.
     *
     * @param mixed $offset Offset count.
     * @param mixed $value  Value.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->run();

        if (null === $offset) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Retrieves the current offset.
     *
     * @param mixed $offset Offset.
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        $this->run();

        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Checks whether an offset exists.
     *
     * @param integer $offset Offset.
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $this->run();

        return isset($this->items[$offset]);
    }

    /**
     * Unsets offset.
     *
     * @param mixed $offset Offset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->run();

        unset($this->items[$offset]);
    }

    /* Countable methods */

    /**
     * Get count.
     *
     * @return integer
     */
    public function count()
    {
        $this->run();

        return count($this->items);
    }

    /* Iterator methods */

    /**
     * Rewind method.
     *
     * @return mixed|void
     */
    public function rewind()
    {
        return reset($this->items);
    }

    /**
     * Get current element method.
     *
     * @return mixed
     */
    public function current()
    {
        $this->run();

        return current($this->items);
    }

    /**
     * Get key method.
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * Get next method.
     *
     * @return mixed|void
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * Validates element method.
     *
     * @return boolean
     */
    public function valid()
    {
        $this->run();

        return key($this->items) !== null;
    }

    /**
     * Returns instance of an Core\Base\Model object.
     *
     * @return Core\Base\Model
     */
    public function getObject()
    {
        return new $this->objectsClass;
    }

    /**
     * Build select statement.
     *
     * @param string $fields Which field to fetch from the DB(default: 'all').
     *
     * @return Query
     */
    public function select($fields)
    {
        $obj = clone $this;

        $obj->queryOptions['type'] = 'select';
        $obj->queryOptions['db_fields'] = $fields;

        return $obj;
    }

    /**
     * Builds FROM SQL statement.
     *
     * @param string $table From which table to get results.
     *
     * @return Query
     */
    public function from($table)
    {
        $obj = clone $this;

        $obj->queryOptions['table'] = $table;

        return $obj;
    }

    /**
     * Builds JOIN SQL statement.
     *
     * @param string $table     Table name to join.
     * @param string $condition Condition upon joining.
     * @param string $type      Type of the join.
     *
     * @TODO add support for prepared statements.
     *
     * @return Query
     */
    public function join($table, $condition, $type = 'INNER')
    {
        $obj = clone $this;
        $obj->queryOptions['join'][] = array(
            'table'     => $table,
            'condition' => $condition,
            'type'      => strtoupper($type),
        );

        return $obj;
    }

    /**
     * Build where statement.
     *
     * @param string $filter Filter conditions.
     * @param array  $params List of params to bind.
     *
     * @return Query
     */
    public function where($filter, array $params = array())
    {
        $obj = clone $this;
        array_push($obj->queryOptions['where'], $filter);
        $obj->queryOptions['bind_params'] = array_merge($obj->queryOptions['bind_params'], $params);

        return $obj;
    }

    /**
     * Build order statement.
     *
     * @param string $field     Column name.
     * @param string $direction Order direction(ASC|DESC).
     *
     * @return object Query
     */
    public function order($field, $direction)
    {
        $obj = clone $this;
        $obj->queryOptions['order'][] = array(
            'field'     => $field,
            'direction' => $direction,
        );

        return $obj;
    }

    /**
     * Build limit/offset statement.
     *
     * @param integer $limit  Number of record to return.
     * @param integer $offset From which record to start.
     *
     * @return Query
     */
    public function limit($limit, $offset = null)
    {
        $obj = clone $this;
        $obj->queryOptions['limit'] = $limit;
        $obj->queryOptions['offset'] = $offset;

        return $obj;
    }

    /**
     * Build set statement, used in Update queries.
     *
     * @param array $fields List of fields to update.
     * @param array $values List of params to bind.
     *
     * @return Query
     */
    public function set(array $fields, array $values)
    {
        $obj = clone $this;
        $obj->queryOptions['db_fields'] = $fields;
        $obj->queryOptions['bind_params'] = array_merge($obj->queryOptions['bind_params'], $values);

        return $obj;
    }

    /**
     * Specifies the table name.
     *
     * @param string $tableName Name of the table.
     *
     * @example when defining a table name
     *
     * @return Query
     */
    public function into($tableName)
    {
        $obj = clone $this;
        $obj->queryOptions['table'] = $tableName;

        return $obj;
    }

    /**
     * Inserts a record.
     *
     * @param mixed $fields Array of fields.
     * @param array $values Array of values.
     *
     * @return Query
     */
    public function insert($fields, array $values)
    {
        $obj = clone $this;
        $obj->queryOptions['type']        = 'insert';
        $obj->queryOptions['db_fields']   = $fields;
        $obj->queryOptions['bind_params'] = array_merge($obj->queryOptions['bind_params'], $values);

        return $obj;
    }

    /**
     * Updates a record.
     *
     * @param string $tableName Name of the table.
     *
     * @return Query
     */
    public function update($tableName)
    {
        $obj = clone $this;
        $obj->queryOptions['type'] = 'update';
        $obj->queryOptions['table'] = $tableName;

        return $obj;
    }

    /**
     * Destroys a record.
     *
     * @return Query
     */
    public function remove()
    {
        $obj = clone $this;
        $obj->queryOptions['type'] = 'remove';

        return $obj;
    }

    /**
     * Creates a table.
     *
     * @param string $tableName Name of the table.
     *
     * @return Query
     */
    public function createTable($tableName)
    {
        $obj = clone $this;
        $obj->queryOptions['type'] = 'create_table';
        $obj->queryOptions['table'] = $tableName;

        return $obj;
    }

    /**
     * Drops a table.
     *
     * @param string $tableName Name of the table.
     *
     * @return Query
     */
    public function dropTable($tableName)
    {
        $obj = clone $this;
        $obj->queryOptions['type'] = 'drop_table';
        $obj->queryOptions['table'] = $tableName;

        return $obj;
    }

    /**
     * Adds a column for a table.
     *
     * @param string $tableName Name of the table.
     *
     * @return Query
     */
    public function addColumns($tableName)
    {
        $obj = clone $this;
        $obj->queryOptions['type'] = 'add_columns';
        $obj->queryOptions['table'] = $tableName;

        return $obj;
    }

    /**
     * Drops a column.
     *
     * @param string $tableName Name of the table.
     *
     * @return Query
     */
    public function dropColumns($tableName)
    {
        $obj = clone $this;
        $obj->queryOptions['type'] = 'drop_columns';
        $obj->queryOptions['table'] = $tableName;

        return $obj;
    }

    /**
     * Set columns.
     *
     * @param mixed $columns Array of column names.
     *
     * @return Query
     */
    public function columns($columns)
    {
        $obj = clone $this;
        $obj->queryOptions['db_fields'] = $columns;

        return $obj;
    }

    /**
     * Sets and DB storage engine.
     *
     * @param string $engine Database storage engine.
     *
     * @return Query
     */
    public function tableEngine($engine)
    {
        $obj = clone $this;
        $obj->queryOptions['table_engine'] = $engine;

        return $obj;
    }

    /**
     * Gets a random record.
     *
     * @TODO implement random function
     *
     * @return void
     */
    public function random()
    {
    }

    /**
     * Retrieves the total count of the result set.
     *
     * @return integer
     */
    public function getCount()
    {
        $obj = clone $this;
        $obj->queryOptions['limit'] = $obj->queryOptions['offset'] = null;

        return count($this->db->run($obj));
    }

    /**
     * Applies pagination behaviour.
     *
     * @param integer $page Page number.
     * @param integer $per  Count of results per page.
     *
     * @return Query
     */
    public function page($page, $per)
    {
        $obj = clone $this;

        $obj->page = $page;
        $obj->perPage = $per;
        $obj->queryOptions['limit'] = $per;
        $obj->queryOptions['offset'] = ($page > 0 ? ($page - 1) : 0) * $per;

        return $obj;
    }

    /**
     * Initializes a Paginator object.
     *
     * @return Features\Paginator\Paginator
     */
    public function paginate()
    {
        return new Features\Paginator\Paginator($this->getCount(), $this->perPage, $this->page);
    }

    /**
     * Fetch first record from the database matching a certain criteria.
     *
     * @return object
     */
    public function first()
    {
        $this->run();

        return $this[0];
    }

    /**
     * Fetch all records from the database.
     *
     * @return array of objects
     */
    public function all()
    {
        $this->run();

        return $this->items;
    }

    /**
     * Checks whether a db record exists.
     *
     * @return boolean
     */
    public function exists()
    {
        $this->run();

        return !empty($this->items);
    }

    /**
     * Checks whether an object exits in the result set.
     *
     * @param Core\Base\Model $item Item to be checked.
     *
     * @return boolean
     */
    public function contains(Core\Base\Model $item)
    {
        $this->run();

        return in_array($item, $this->items);
    }

    /**
     * Appends table name prefix.
     *
     * @param string $prefix Prefix contents.
     *
     * @return void
     */
    public function appendTablesPrefix($prefix)
    {
        if (!$this->tablesPrefixAppended) {
            $this->queryOptions['table'] = $prefix . $this->queryOptions['table'];
            $this->tablesPrefixAppended = true;
        }
    }
}
