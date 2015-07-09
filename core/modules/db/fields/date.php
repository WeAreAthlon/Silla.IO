<?php

namespace Core\Modules\DB\Fields;

class Date extends \Zob\Objects\Field
{
    protected $type = 'date';

    public function validate($value)
    {
        if ($error = parent::validate($value)) {
            return $error;
        }

        /* @TODO Check if the $value is a correct date string */
    }
}

