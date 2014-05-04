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
 * Строка.
 */
class Tfloat extends Type
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        return is_float($var);
    }
}
