<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Строка.
 */
class Tstring extends Type
{
    // --- Открытые методы ---

    /**
     * @see Wheels\Config\Schema\Option\Type::convert()
     */
    public function convert($var)
    {
        return (string) $var;
    }

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    public function validate($var)
    {
        return is_string($var);
    }
}
