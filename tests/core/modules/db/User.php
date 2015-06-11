<?php

namespace Tests\Core\Modules\DB;

use Core\Base\Model;

class User extends Model
{
    protected $hasMany = ['task'];
}
