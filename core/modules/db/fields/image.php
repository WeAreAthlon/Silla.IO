<?php

namespace Core\Modules\DB\Fields;

class Image extends \Zob\Objects\Field
{
    protected $type = 'varchar';

    protected $length = 255;

    public function validate($value)
    {
        return false;
    }
}

