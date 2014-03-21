<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Целые числа.
 */
class Tint extends Type
{
    /**
     * @see Wheels\Config\Schema\Option\Type::convert()
     */
    public function convert($var)
    {
        return (int) $var;
    }

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    public function validate($var)
    {
        return is_int($var);
    }
}
