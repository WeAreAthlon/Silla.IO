<?php

namespace Core\Modules\DB\Fields;

class Email extends \Zob\Objects\Field
{
    protected $type = 'varchar';

    protected $length = 100;

    public function validate($value)
    {
        if ($error = parent::validate($value)) {
            return $error;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'InvalidFormat';
        } 

        return false;
    }
}

