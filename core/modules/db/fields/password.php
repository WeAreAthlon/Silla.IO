<?php
    
namespace Core\Modules\DB\Fields;

class Password extends \Zob\Objects\Field
{
    protected $type = 'varchar';

    protected $length = 40;

    protected function beforeWrite($value)
    {
        return $value;
    }

    protected function afterRead($value)
    {
        return $value;
    }
}

