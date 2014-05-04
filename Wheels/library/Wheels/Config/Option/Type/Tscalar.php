<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option\Type
 */

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\Type;

/**
 * Скалярное значение.
 */
class Tscalar extends Type
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        return is_scalar($var);
    }
}
