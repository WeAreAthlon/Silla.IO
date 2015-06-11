<?php
namespace Core\Modules\DB;

class Entity
{
    protected $relations = [];

    public function getRelation($name)
    {
        return $this->relations[$name];
    }

    protected function hasMany($name)
    {
        $this->relations[$name] = new Relation($name, 'hasMany');
    }

    protected function hasAndBelongsToMany($name)
    {
        $this->relations[$name] = new Relation($name, 'hasAndBelongsToMany');
    }

    protected function belongsTo($name)
    {
        $this->relations[$name] = new Relation($name, 'belongsTo');
    }

    protected function hasOne($name)
    {
        $this->relations[$name] = new Relation($name, 'hasOne');
    }
}

