<?php

namespace Core\Modules\DB;

use DB\Schema;
use ICanBoogie\Inflector;

abstract class Entity
{
    protected $table;

    protected $connection;

    protected $fields = [];

    protected $errors = [];

    public function __construct(\Core\Silla $environment)
    {
        $class = new \ReflectionClass($this);
        $inflector = Inflector::get();
        $this->table = $inflector->pluralize($inflector->underscore($class->getShortName()));
        $this->connection = $environment->getDb()->getConnection();

        foreach (Schema::${$this->table}->getFields() as $field) {
            $this->fields[$field->getName()] = null;
        }
    }

    public function __get($field)
    {
        if (Schema::${$this->table}->getField($field)) {
            return $this->fields[$field];
        }
    }

    public function __set($field, $value)
    {
        if (Schema::${$this->table}->getField($field)) {
            $this->fields[$field] = $value;
        }
    }

    public function save(array $params = [])
    {
        $result = false;

        foreach ($params as $field=>$value) {
            if (array_key_exists($field, $this->fields)) {
                $this->fields[$field] = $value;
            }
        }

        $this->connection->transaction(function() use (&$result) {
            $this->beforeValidate();
            $this->isValid();
            $this->afterValidate();

            if (empty($this->errors)) {
                $this->beforeSave();
                $result = $this->insert();
                $this->afterSave();
            }
        });

        return $result;
    }

    public function isValid()
    {
        foreach (Schema::${$this->table}->getFields() as $field) {
            $error = $field->validate($this->fields[$field->getName()]);

            if ($error) {
                $this->errors[$field->getName()] = $error;
            }
        }
    }

    protected function beforeValidate() {}

    protected function afterValidate() {}

    protected function beforeSave() {}

    protected function afterSave() {}

    protected function insert()
    {
        $query = new Query($this->connection);
        $values = [];

        foreach (Schema::${$this->table}->getFields() as $field) {
            $values[$field->getName()] = $field->beforeWrite($this->fields[$field->getName()]);
        }

        $query->insert(Schema::${$this->table}, $values);

        return $query->run();
    }
}

