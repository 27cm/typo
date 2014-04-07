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
