<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Логический тип.
 */
class Tbool extends Type
{
    /**
     * @see Wheels\Config\Schema\Option\Type::convert()
     */
    public function convert($var)
    {
        return (bool) $var;
    }

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    public function validate($var)
    {
        return is_bool($var);
    }
}
