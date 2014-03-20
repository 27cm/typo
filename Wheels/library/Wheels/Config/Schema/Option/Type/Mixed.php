<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 *
 */
class Mixed extends Type
{
    public function to($value)
    {
        return $value;
    }

    public function validate($value)
    {
        return TRUE;
    }
}
