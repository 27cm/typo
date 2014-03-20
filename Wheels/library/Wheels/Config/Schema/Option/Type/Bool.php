<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 *
 */
class Bool extends Type
{
    /**
     * @see Wheels\Config\Schema\Option\Type::convert()
     */
    static public function convert($value)
    {
        return (bool) $value;
    }

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    static public function validate($value)
    {
        return is_bool($value);
    }
}
