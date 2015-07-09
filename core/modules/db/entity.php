<?php

namespace Core\Modules\DB;

class Entity
{
    protected $table;

    protected $fields = [];

    protected $errors = [];

    public function __construct()
    {
        $this->table = DB::getTable(get_class($this));
    }

    public function __get($field)
    {
        if ($this->table->getField($field)) {
            return $this->fields[$field];
        }
    }

    public function __set($field, $value)
    {
        if ($this->table->getField($field)) {
            $this->fields[$field] = $value;
        }
    }

    public function all()
    {
        $query = new Query(DB::$connection);
        $query->select($table);
    }

    public function one()
    {
    
    }

    public function save(array $params = [])
    {
        foreach ($params as $field=>$value) {
            if (isset($this->fields[$field])) {
                $this->fields[$field] = $value;
            }
        }
    }

    public function isValid()
    {
        foreach ($this->table->getFields() as $field) {
            $error = $field->validate($this->fields[$field->getName()]);

            if ($error) {
                $this->errors[$field->getName()] = $error;
            }
        }
    }
}

