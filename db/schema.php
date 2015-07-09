<?php

namespace DB;

use \Zob\Objects;

class Schema
{
    public static $users;
}

Schema::$users = new Objects\Table('users', [
    new Objects\Field([
        'name' => 'id',
        'type' => 'int',
        'length' => 10,
        'pk'    => true,
        'ai'    => true
    ]),
    new Objects\Field([
        'name' => 'name',
        'type' => 'varchar',
        'length' => 255
    ]),
    new Objects\Field([
        'name' => 'email',
        'type' => 'varchar',
        'length' => 255,
        'required' => true
    ]),
    new Objects\Field([
        'name' => 'created_at',
        'type' => 'datetime'
    ])
]);

