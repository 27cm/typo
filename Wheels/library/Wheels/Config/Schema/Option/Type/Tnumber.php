<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Schema\Option\Type
 */

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Число типа int либо float.
 */
class Tnumber extends Type
{
    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        return (is_int($var) || is_float($var));
    }
}
