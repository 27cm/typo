<?php

namespace Wheels\Config\Schema\Type;

use Wheels\Config\Schema\Type;

/**
 *
 */
class Bool extends Type
{
    public function to($value)
    {
        return (bool) $value;
    }

    public function validate($value)
    {
        return is_bool($value);
    }
}
