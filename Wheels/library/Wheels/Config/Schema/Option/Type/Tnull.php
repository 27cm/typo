<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Тип переменной без значения.
 */
class Tnull extends Type
{
    // --- Открытые методы ---

    /**
     * @see Wheels\Config\Schema\Option\Type::convert()
     */
    public function convert($var)
    {
        $var = NULL;
        return $var;
    }

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    public function validate($var)
    {
        return is_null($var);
    }
}
