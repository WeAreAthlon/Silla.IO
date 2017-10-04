<?php
/**
 * Base abstraction layer for database operations.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;
use Core\Modules\DB;

/**
 * Class Model definition.
 */
abstract class Model
{
    /**
     * Observer listeners.
     *
     * @var array
     * @access public
     */
    public $listeners = array();

    /**
     * Name of the table.
     *
     * @var string
     * @access protected
     * @static
     */
    public static $tableName = null;

    /**
     * The primary key field.
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $primaryKeyField = 'id';

    /**
     * Stores all database fields of the object.
     *
     * @var array
     * @access protected
     */
    protected $fields = array();

    /**
     * Stores the errors that may occur during query executions or validation.
     *
     * @var array
     * @access protected
     */
    protected $errors = array();

    /**
     * Stores all associations of type "belongs to".
     *
     * @var array
     * @access public
     */
    public $belongsTo = array();

    /**
     * Stores all associations of type "has many".
     *
     * @var array
     * @access public
     */
    public $hasMany = array();

    /**
     * Stores all associations of type "has and belongs to many" (habtm).
     *
     * @var array
     * @access public
     */
    public $hasAndBelongsToMany = array();

    /**
     * Whether the table we're looking at has a corresponding table, holding internationalized values.
     *
     * @var boolean
     * @access public
     * @static
     */
    public static $isI18n = false;

    /**
     * Name of the corresponding i18n table.
     *
     * @var string
     * @access public
     * @static
     */
    public static $i18nTableName;

    /**
     * Keeps the currently selected locale identifier.
     *
     * @var string
     * @access public
     * @static
     */
    public static $i18nLocale;

    /**
     * Info about multilingual columns.
     *
     * @var array
     * @access protected
     */
    protected $fieldsI18n = null;

    /**
     * Name of the foreign key field for i18n tables.
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $i18nForeignKeyField = 'i18n_foreign_key';

    /**
     * Name of the locale field for i18n tables.
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $i18nLocaleField = 'i18n_locale';

    /**
     * Suffix to be appended to the corresponding i18n table if not i18nTableName presented.
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $i18nTableNameSuffix = '_i18n';

    /**
     * Join type for i18n data.
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $i18nJoinType = 'INNER';

    /**
     * Localisation.
     *
     * @var string
     */
    protected $i18nLocalisation;

    /**
     * Query object to be used for communication with the DB layer.
     *
     * @var DB\Query
     * @access protected
     */
    protected $query;

    /**
     * Constructor method.
     *
     * @param array $params Array of fields and their values.
     *
     * @access public
     * @uses   Core\Config()
     * @final
     * @throws \LogicException Each instance must have a public static $tableName value.
     */
    final public function __construct(array $params = array())
    {
        if (!static::$tableName) {
            throw new \LogicException(get_class($this) . ' must have a public static $tableName value.');
        }

        if (static::$isI18n) {
            if (!static::$i18nLocale) {
                $_locale            = Core\Registry()->get('locale');
                static::$i18nLocale =
                    isset(Core\Config()->I18N['locales'][$_locale]) ? $_locale : Core\Config()->I18N['default'];
            }

            if (!static::$i18nTableName) {
                static::$i18nTableName = static::$tableName . static::$i18nTableNameSuffix;
            }
        }

        $this->attachListeners();

        $interfaces = class_implements($this);

        foreach ($interfaces as $interface) {
            if (strpos($interface, 'Decorators')) {
                $decorator = str_replace('\\Interfaces', '', $interface);
                $decorator::decorate($this);
            }
        }

        $this->fire('beforeCreate', array($this));
        $this->createFields();

        if (!empty($params)) {
            $this->populateFields($params);
        }

        $this->fire('afterCreate', array($this));
    }

    /**
     * Whether the record exists in the persistent storage.
     *
     * @return boolean
     */
    final public function exists()
    {
        return (bool)$this->{static::$primaryKeyField};
    }

    /**
     * Generic set method.
     *
     * @param string $field Field to set.
     * @param mixed  $value Value of the field.
     *
     * @final
     *
     * @return void
     */
    final public function __set($field, $value)
    {
        if (array_key_exists($field, $this->fields)) {
            $this->fields[$field] = $value;
        } else {
            $this->{$field} = $value;
        }
    }

    /**
     * Generic get method.
     *
     * Check if the field is in the object database fields return it from the fields array.
     *
     * @param string $field Field to get.
     *
     * @final
     *
     * @return mixed
     */
    final public function __get($field)
    {
        if (array_key_exists($field, $this->fields)) {
            return $this->fields[$field];
        }

        return isset($this->{$field}) ? $this->{$field} : null;
    }

    /**
     * Isset method, needed for all internal __set() and __get() calls.
     *
     * @param string $field Field to check.
     *
     * @final
     *
     * @return boolean
     */
    final public function __isset($field)
    {
        if (array_key_exists($field, $this->fields)) {
            return isset($this->fields[$field]);
        }

        return false;
    }

    /**
     * Unset method, needed for all internal __set and __get calls.
     *
     * @param string $field Field to unset.
     *
     * @final
     *
     * @return void
     */
    final public function __unset($field)
    {
        if (array_key_exists($field, $this->fields)) {
            unset($this->fields[$field]);
        }
    }

    /**
     * Magic method __call used for accessing the results of the associations.
     *
     * @param string $name The name of the uninitialized method.
     * @param array  $args Array of the parameters passed to the method.
     *
     * @final
     *
     * @return object|array
     */
    final public function __call($name, array $args)
    {
        if (array_key_exists($name, $this->belongsTo)) {
            return $this->getAssociation($this->belongsTo[$name], $name);
        }

        if (array_key_exists($name, $this->hasMany)) {
            return $this->getAssociation($this->hasMany[$name], $name);
        }

        if (array_key_exists($name, $this->hasAndBelongsToMany)) {
            return $this->hasAndBelongsToMany($this->hasAndBelongsToMany[$name], $name);
        }

        return array();
    }

    /**
     * Attaches an event.
     *
     * @param string $event    Event name.
     * @param mixed  $callback Callback function name to execute.
     *
     * @return void
     */
    final public function on($event, $callback)
    {
        Core\Modules\DB\Observer::on($this, $event, $callback);
    }

    /**
     * Removes an event.
     *
     * @param string $event    Event name.
     * @param mixed  $callback Callback function name to execute.
     *
     * @return void
     */
    final public function detach($event, $callback)
    {
        Core\Modules\DB\Observer::detach($this, $event, $callback);
    }

    /**
     * Fires an event.
     *
     * @param string $event     Event name.
     * @param array  $arguments Array of arguments.
     *
     * @return void
     */
    final public function fire($event, array $arguments = array())
    {
        Core\Modules\DB\Observer::fire($this, $event, $arguments);
    }

    /**
     * Retrieve resource validation errors.
     *
     * @return array
     */
    final public function errors()
    {
        return $this->errors;
    }

    /**
     * Check whether the resource has validation errors.
     *
     * @return boolean
     */
    final public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Retrieve resource validation error by field name.
     *
     * @param string $field Field name.
     *
     * @final
     *
     * @return mixed
     */
    final public function getError($field)
    {
        return isset($this->errors[$field]) ? $this->errors[$field] : null;
    }

    /**
     * Set a validation error.
     *
     * @param string $field Field name.
     * @param mixed  $type  Error type.
     *
     * @return void
     */
    final public function setError($field, $type)
    {
        $this->errors[$field] = $type;
    }

    /**
     * Remove a validation error.
     *
     * @param string $field Field name.
     *
     * @return void
     */
    final public function removeError($field)
    {
        unset($this->errors[$field]);
    }

    /**
     * Attaches all registered listeners.
     *
     * @return void
     */
    private function attachListeners()
    {
        $this->on('beforeCreate', array($this, 'beforeCreate'));
        $this->on('afterCreate', array($this, 'afterCreate'));

        $this->on('beforePopulate', array($this, 'beforePopulate'));
        $this->on('afterPopulate', array($this, 'afterPopulate'));

        $this->on('beforeValidate', array($this, 'beforeValidate'));
        $this->on('afterValidate', array($this, 'afterValidate'));

        $this->on('beforeSave', array($this, 'beforeSave'));
        $this->on('afterSave', array($this, 'afterSave'));

        $this->on('beforeUpdate', array($this, 'beforeUpdate'));
        $this->on('afterUpdate', array($this, 'afterUpdate'));

        $this->on('beforeDelete', array($this, 'beforeDelete'));
        $this->on('afterDelete', array($this, 'afterDelete'));
    }

    /**
     * Get the associated object.
     *
     * @param array  $association Association params.
     * @param string $name        Name of the association.
     *
     * @access private
     *
     * @return object
     */
    private function getAssociation(array $association, $name)
    {
        $key          = $association['key'];
        $relative_key = $association['relative_key'];
        $class_name   = isset($association['class_name']) ? $association['class_name'] : $name;

        return $this->{$name} = $class_name::find()->where("{$relative_key} = ?", array($this->{$key}));
    }

    /**
     * Get the all associated objects of the "has and belongs to many" association.
     *
     * @param array  $association Association params.
     * @param string $name        Name of the association.
     *
     * @access private
     *
     * @return array
     */
    private function hasAndBelongsToMany(array $association, $name)
    {
        $prefix            = Core\Config()->DB['tables_prefix'];
        $association_table = $association['table'];
        $key               = $association['key'];
        $relative_key      = $association['relative_key'];
        $class_name        = isset($association['class_name']) ? $association['class_name'] : $name;

        $mdl = new $class_name();

        $_fieldsI18n = $class_name::$isI18n ? ', ' . $class_name::$i18nTableName . '.*' : '';

        $query = $mdl::find($prefix . $class_name::$tableName . '.*' . $_fieldsI18n)
                     ->join(
                         $association_table,
                         "{$prefix}{$association_table}.{$relative_key} = {$prefix}{$mdl::$tableName}." .
                         $class_name::primaryKeyField()
                     )
                     ->where("{$prefix}{$association_table}.{$key} = ?", array($this->{static::$primaryKeyField}));

        return $this->{$name} = $query;
    }

    /**
     * Save or update the associated objects.
     *
     * @access private
     *
     * @return void
     */
    private function saveAssociations()
    {
        /* Save the "has many" from habtm objects */
        foreach ($this->hasAndBelongsToMany as $k => $rel) {
            if (isset($this->{$k}) && is_array($this->{$k})) {
                $association_table = $rel['table'];
                $key               = $rel['key'];
                $relative_key      = $rel['relative_key'];
                $primary_key       = $this->{static::$primaryKeyField};

                $query  = new DB\Query();
                $result = Core\DB()->run(
                    $query->select($relative_key)->from($association_table)->where("{$key} = ?", array($primary_key))
                );

                $original_ids = array();

                if ($result) {
                    $original_ids = Core\Utils::arrayFlatten($result);
                }

                $habtm_values = array();

                foreach ($this->{$k} as $item) {
                    $habtm_values[] = (is_object($item) ? $item->id : $item);
                }

                $to_add = array_diff($habtm_values, $original_ids);

                if (!empty($to_add)) {
                    Core\DB()->run(
                        $query
                            ->insert(array($key, $relative_key), array_map(function ($item) use ($primary_key) {
                                return array($primary_key, $item);
                            }, $to_add))
                            ->into($association_table)
                    );
                }
            }
        }
    }

    /**
     * Delete associated objects and records.
     *
     * @access private
     *
     * @return void
     */
    private function deleteAssociations()
    {
        /* Delete objects of the habtm associations */
        foreach ($this->hasAndBelongsToMany as $k => $rel) {
            if (isset($this->{$k}) && is_array($this->{$k})) {
                $association_table = $rel['table'];
                $key               = $rel['key'];
                $relative_key      = $rel['relative_key'];
                $primary_key       = $this->{static::$primaryKeyField};

                /* Gets the id of the original associated objects */
                $query  = new DB\Query();
                $result = Core\DB()->run(
                    $query->select($relative_key)->from($association_table)->where("{$key} = ?", array($primary_key))
                );

                if ($result) {
                    $original_ids = Core\Utils::arrayFlatten($result);

                    /* Fills the ids of the current associated objects */
                    $passed_ids = array();

                    foreach ($this->{$k} as $item) {
                        $passed_ids[] = (is_object($item) ? $item->id : $item);
                    }

                    /* Get the difference */
                    $to_delete = array_diff($original_ids, $passed_ids);

                    /* Delete if there are differences */
                    if (!empty($to_delete)) {
                        /* @TODO implement the "where in (smt., smt.)" in the DB driver */
                        Core\DB()->run(
                            $query
                                ->remove()
                                ->from($association_table)
                                ->where("{$key} = ?", array($primary_key))
                                ->where("{$relative_key} IN (" . implode(',', array_map(function () {
                                    return '?';
                                }, $to_delete)) . ")", $to_delete)
                        );
                    }
                }
            }
        }
    }

    /**
     * Deletes related data deeply.
     *
     * @access private
     *
     * @return void
     */
    private function deleteAssociationsDeep()
    {
        /* Has and belongs to many associations */
        foreach ($this->hasAndBelongsToMany as $k => $rel) {
            $association_table = $rel['table'];
            $key               = $rel['key'];

            $query = new DB\Query();

            Core\DB()->run(
                $query->remove()->from($association_table)
                      ->where("{$key} = ?", array($this->{static::$primaryKeyField}))
            );
        }
    }

    /**
     * Creates an object internal properties to reflect the DB.
     *
     * @access private
     *
     * @return void
     */
    private function createFields()
    {
        $schema_meta = $this->getSchema();
        if (!empty($schema_meta) && is_array($schema_meta)) {
            $keys = array_keys($schema_meta);

            foreach ($keys as $field) {
                $this->fields[$field] = null;
            }
        }

        if (static::$isI18n) {
            $schema_meta = $this->getI18nSchema();

            if (!empty($schema_meta)) {
                $keys = array_keys($schema_meta);

                foreach ($keys as $field) {
                    $this->fields[$field] = null;
                    $this->fieldsI18n[]   = $field;
                }
            }
        }
    }

    /**
     * Populates the object fields with content mainly called from the constructor.
     *
     * @param array $params Example [fields => values].
     *
     * @access private
     *
     * @return void
     */
    private function populateFields(array $params)
    {
        if (static::$isI18n && isset($params[static::$i18nForeignKeyField], $params[static::$i18nLocaleField])) {
            unset($params[static::$i18nForeignKeyField], $params[static::$i18nLocaleField]);
        }

        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Updates the object fields with content called on save method.
     *
     * @param array $params Example array(field => value).
     *
     * @access private
     *
     * @return void
     */
    private function updateFields(array $params)
    {
        if (array_key_exists('updated_on', $this->fields)) {
            $params = array_merge(array('updated_on' => date('Y-m-d H:i:s')), $params);
        }

        if (empty($this->created_on) && array_key_exists('created_on', $this->fields)) {
            $params = array_merge(array('created_on' => date('Y-m-d H:i:s')), $params);
        }

        $this->populateFields($params);
    }

    /**
     * Returns the object fields/values suitable for database queries.
     *
     * @access private
     *
     * @return array Example [$fields => $values].
     */
    private function extractFields()
    {
        $fields      = array();
        $values      = array();
        $fields_self = $this->fields;

        if (static::$isI18n) {
            $fields_self = array_diff_key($this->fields, array_flip($this->fieldsI18n));
        }

        foreach ($fields_self as $field => $value) {
            if ($this->isAutoIncrement($field)) {
                continue;
            }

            $fields[] = $field;
            $values[] = $value;
        }

        return array($fields, $values);
    }

    /**
     * Prepare the object i18n fields suitable for database queries.
     *
     * @param boolean $include_associations Whether to include the translated resource.
     *
     * @access private
     *
     * @return array
     */
    private function extractFieldsI18n($include_associations = false)
    {
        $fields = array();
        $values = array();

        if ($include_associations) {
            /* Assign parent related fields */
            $fields[] = static::$i18nForeignKeyField;
            $values[] = $this->fields[static::$primaryKeyField];

            /* Assign locale */
            $fields[] = static::$i18nLocaleField;
            $values[] = static::$i18nLocale;
        }

        /* Assign all other i18n fields */
        foreach ($this->fieldsI18n as $key) {
            $fields[] = $key;
            $values[] = $this->fields[$key];
        }

        return array($fields, $values);
    }

    /**
     * Inserts the object into the database.
     *
     * @access private
     *
     * @return boolean
     */
    private function insert()
    {
        list($fields, $values) = $this->extractFields();

        $query       = new DB\Query();
        $this->query = $query->insert($fields, $values)->into(static::$tableName);
        $result      = Core\DB()->run($this->query);

        $this->{static::$primaryKeyField} = Core\DB()->getLastInsertId();

        if (static::$isI18n) {
            list(, $values) = $this->extractFieldsI18n();

            if (array_filter($values)) {
                list($fields, $values) = $this->extractFieldsI18n(true);
                $query_i18n = new DB\Query();
                $query_i18n = $query_i18n->insert($fields, $values)->into(static::$i18nTableName);

                Core\DB()->run($query_i18n);
            }

            return $result;
        }

        return $result;
    }

    /**
     * Updates the record in the database.
     *
     * @access private
     *
     * @return boolean
     */
    private function update()
    {
        list($fields, $values) = $this->extractFields();

        $query       = new DB\Query();
        $this->query = $query
            ->update(static::$tableName)
            ->set($fields, $values)
            ->where(static::$primaryKeyField . ' = ?', array($this->{static::$primaryKeyField}));

        if (static::$isI18n) {
            $query_i18n           = new DB\Query();
            $i18n_existing_record = $query_i18n
                ->select(static::$i18nLocaleField)
                ->from(static::$i18nTableName)
                ->where(
                    static::$i18nForeignKeyField . ' = ' . $this->{static::$primaryKeyField} .
                    ' AND ' .
                    static::$i18nLocaleField . ' = "' . static::$i18nLocale . '"'
                )
                ->first();

            if (!$i18n_existing_record) {
                list($fields, $values) = $this->extractFieldsI18n(true);
                $query_i18n = $query_i18n->insert($fields, $values)->into(static::$i18nTableName);
            } else {
                list($fields, $values) = $this->extractFieldsI18n();
                $query_i18n = $query_i18n
                    ->update(static::$i18nTableName)
                    ->set($fields, $values)
                    ->where(
                        static::$i18nForeignKeyField . ' = ' . $this->{static::$primaryKeyField} .
                        ' AND ' .
                        static::$i18nLocaleField . ' = "' . static::$i18nLocale . '"'
                    );
            }

            return Core\DB()->run($this->query) && Core\DB()->run($query_i18n);
        }

        return Core\DB()->run($this->query);
    }

    /**
     * Removes the record from the database.
     *
     * @access private
     *
     * @return boolean
     */
    private function remove()
    {
        $query       = new DB\Query();
        $this->query = $query
            ->remove()
            ->from(static::$tableName)
            ->where(static::$primaryKeyField . ' = ?', array($this->{static::$primaryKeyField}));

        if (static::$isI18n) {
            $query_i18n = new DB\Query();
            $query_i18n = $query_i18n
                ->remove()
                ->from(static::$i18nTableName)
                ->where(static::$i18nForeignKeyField . ' = ?', array($this->{static::$primaryKeyField}));

            return Core\DB()->run($this->query) && Core\DB()->run($query_i18n);
        }

        return Core\DB()->run($this->query);
    }

    /**
     * System validation for some common restrictions.
     *
     * @access private
     *
     * @return void
     */
    private function validate()
    {
        $schema = $this->getSchema();

        if (static::$isI18n) {
            $schema = array_merge($schema, $this->getI18nSchema());
        }

        foreach ($this->fields as $field => $value) {
            if ($this->isAutoIncrement($field)) {
                continue;
            }

            /* Trim spacing. */
            $value = trim($value);

            /*
             * We are using this "( is_null($value) || '' === $value )", instead of just empty($field)
             * because if $field is 0, empty(0) is true, and we can't pass 0's to a NOT_null field
             */
            if (($schema[$field]['is_null'] === 'NO') && (is_null($value) || ('' === $value)) &&
                empty($schema[$field]['default'])
            ) {
                $this->errors[$field] = 'not_empty';
                continue;
            }

            switch ($schema[$field]['type']) {
                case 'string':
                    if ($value && !is_string($value)) {
                        $this->errors[$field] = 'invalid_type';
                    }

                    if (mb_strlen($value) > $schema[$field]['length']) {
                        $this->errors[$field] = 'max_length_exceeded';
                    }

                    break;
                case 'int':
                    if ($value && !is_numeric($value)) {
                        $this->errors[$field] = 'invalid_type';
                    }

                    break;
                case 'enum':
                    if ($value && !in_array($value, $schema[$field]['values'])) {
                        $this->errors[$field] = 'enum_no_match';
                    }

                    break;
            }

            /* Check for uniqueness */
            if ($schema[$field]['unique']) {
                $existing_record = static::find()->where("{$field} = ?", array($value))->first();

                if ($existing_record &&
                    $existing_record->{static::$primaryKeyField} != $this->{static::$primaryKeyField}
                ) {
                    $this->errors[$field] = 'duplication';
                }
            }
        }
    }

    /**
     * Check if the field is auto-increment.
     *
     * @param string $field Field to check.
     *
     * @access private
     *
     * @return boolean
     */
    private function isAutoIncrement($field)
    {
        $schema = $this->getSchema();

        if (static::$isI18n) {
            $schema = array_merge($schema, $this->getI18nSchema());
        }

        return (strpos($schema[$field]['extra'], 'auto_increment') !== false);
    }

    /**
     * Before creation hook.
     *
     * @return void
     */
    public function beforeCreate()
    {
    }

    /**
     * After creation hook.
     *
     * @return void
     */
    public function afterCreate()
    {
    }

    /**
     * Before fields population hook.
     *
     * @return void
     */
    public function beforePopulate()
    {
    }

    /**
     * After fields population hook.
     *
     * @return void
     */
    public function afterPopulate()
    {
    }

    /**
     * Before validation hook.
     *
     * @return void
     */
    public function beforeValidate()
    {
    }

    /**
     * After validation hook.
     *
     * @return void
     */
    public function afterValidate()
    {
    }

    /**
     * Before save hook.
     *
     * @return void
     */
    public function beforeSave()
    {
    }

    /**
     * After save hook.
     *
     * @return void
     */
    public function afterSave()
    {
    }

    /**
     * Before update hook.
     *
     * @return void
     */
    public function beforeUpdate()
    {
    }

    /**
     * After update hook.
     *
     * @return void
     */
    public function afterUpdate()
    {
    }

    /**
     * Before deletion hook.
     *
     * @return void
     */
    public function beforeDelete()
    {
    }

    /**
     * After deletion hook.
     *
     * @return void
     */
    public function afterDelete()
    {
    }

    /**
     * Save the object in the Database, and fires events.
     *
     * @param array   $params          Associative array with the [fields => values] to save.
     * @param boolean $skip_validation Flag to execute validation process.
     *
     * @access public
     * @final
     *
     * @return boolean
     */
    final public function save(array $params = array(), $skip_validation = false)
    {
        $result = null;

        $this->fire('beforePopulate', array($this, $params));

        $this->updateFields($params);

        $this->fire('afterPopulate', array($this, $params));

        if (!$skip_validation) {
            $this->fire('beforeValidate', array($this, $params));

            $this->validate();

            $this->fire('afterValidate', array($this, $params));
        }

        if (!empty($this->errors)) {
            return false;
        }

        $this->fire('beforeSave', array($this, $params));

        if ($this->isNewRecord()) {
            $result = $this->insert();
        } else {
            $this->fire('beforeUpdate', array($this, $params));

            $result = $this->update();

            $this->fire('afterUpdate', array($this));
        }

        if ($result) {
            $this->deleteAssociations();
            $this->saveAssociations();

            $this->fire('afterSave', array($this));
        }

        return $result;
    }

    /**
     * Delete the object from the Database, and fires events.
     *
     * @access public
     * @final
     *
     * @return boolean
     */
    final public function delete()
    {
        $this->fire('beforeDelete', array($this));

        $result = $this->remove();

        $this->deleteAssociationsDeep();

        $this->fire('afterDelete', array($this));

        return $result;
    }

    /**
     * Get object/objects from the Database.
     *
     * @param string $fields List of fields to return (optional).
     *
     * @access public
     * @final
     * @static
     *
     * @return DB\Query
     */
    final public static function find($fields = 'all')
    {
        $query = new DB\Query(get_called_class());

        if (static::$isI18n) {
            if (!static::$i18nLocale) {
                $_locale            = Core\Registry()->get('locale');
                static::$i18nLocale =
                    isset(Core\Config()->I18N['locales'][$_locale]) ? $_locale : Core\Config()->I18N['default'];
            }

            if (!static::$i18nTableName) {
                static::$i18nTableName = static::$tableName . static::$i18nTableNameSuffix;
            }

            $prefix = Core\Config()->DB['tables_prefix'];

            return $query
                ->select($fields)
                ->from(static::$tableName)
                ->join(
                    static::$i18nTableName,
                    $prefix . static::$tableName . '.' . static::$primaryKeyField
                    . ' = '
                    . $prefix . static::$i18nTableName . '.' . static::$i18nForeignKeyField
                    . ' AND '
                    . $prefix . static::$i18nTableName . '.' . static::$i18nLocaleField
                    . ' = "'
                    . static::$i18nLocale . '"',
                    static::$i18nJoinType
                );
        }

        return $query->select($fields)->from(static::$tableName);
    }

    /**
     * Check if the object is a new record for the storage engine.
     *
     * @access public
     * @final
     *
     * @return boolean
     */
    final public function isNewRecord()
    {
        return !$this->{static::$primaryKeyField};
    }

    /**
     * Gets the primary key field name.
     *
     * @access public
     * @final
     * @static
     *
     * @return string
     */
    final public static function primaryKeyField()
    {
        return static::$primaryKeyField;
    }

    /**
     * Gets the i18n foreign key field name.
     *
     * @access public
     * @final
     * @static
     *
     * @return string
     */
    final public static function i18nForeignKeyField()
    {
        return static::$i18nForeignKeyField;
    }

    /**
     * Gets the i18n locale field name.
     *
     * @access public
     * @final
     * @static
     *
     * @return string
     */
    final public static function i18nLocaleField()
    {
        return static::$i18nLocaleField;
    }

    /**
     * Retrieves an object listener.
     *
     * @param string $event Event name.
     *
     * @access public
     *
     * @return array
     */
    public function listeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : array();
    }

    /**
     * Retrieves the database table schema.
     *
     * @access public
     * @static
     * @uses   Core\DbCache()
     *
     * @return array
     */
    public static function getSchema()
    {
        return Core\DbCache()->getSchema(static::$tableName);
    }

    /**
     * Fetches the i18n_table schema.
     *
     * @access public
     * @static
     * @uses   Core\DbCache()
     *
     * @return array
     */
    public static function getI18nSchema()
    {
        $schema = Core\DbCache()->getSchema(static::$i18nTableName);

        if (!empty($schema) && isset($schema[static::$i18nForeignKeyField], $schema[static::$i18nLocaleField])) {
            unset($schema[static::$i18nForeignKeyField], $schema[static::$i18nLocaleField]);
        }

        return $schema;
    }

    /**
     * Default timezone aware fields.
     *
     * @access public
     * @static
     *
     * @return array
     */
    public static function timezoneAwareFields()
    {
        return array('created_on', 'updated_on');
    }

    /**
     * Exposes object DB fields.
     *
     * @return array Fields.
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Exposes object internationalized DB fields.
     *
     * @return array Fields.
     */
    public function fieldsI18N()
    {
        return $this->fieldsI18n;
    }

    /**
     * Retrieve primary key value for an object.
     *
     * @return mixed
     */
    public function getPrimaryKeyValue()
    {
        return $this->{$this::$primaryKeyField};
    }

    /**
     * Retrieve association meta data by associated key.
     *
     * @param string $key Associated key.
     *
     * @return array|false
     */
    public function getAssociationMetaDataByKey($key)
    {
        $associationsByType = array(
            'has_many'   => $this->hasMany,
            'habtm'      => $this->hasAndBelongsToMany,
            'belongs_to' => $this->belongsTo,
        );

        foreach ($associationsByType as $type => $associations) {
            foreach ($associations as $name => $association) {
                if ($association['key'] == $key) {
                    $association['name'] = $name;
                    $association['type'] = $type;

                    return $association;
                }
            }
        }

        return false;
    }

    /**
     * Modifies the current I18N locale value.
     *
     * @param string $i18nLocale Locale value.
     *
     * @return void
     */
    public function setI18nLocale($i18nLocale)
    {
        static::$i18nLocale     = $i18nLocale;
        $this->i18nLocalisation = $i18nLocale;
    }

    /**
     * Retrieve the current I18N locale value.
     *
     * @return string Locale value.
     */
    public function getI18nLocale()
    {
        return $this->i18nLocalisation ? $this->i18nLocalisation : static::$i18nLocale;
    }
}
