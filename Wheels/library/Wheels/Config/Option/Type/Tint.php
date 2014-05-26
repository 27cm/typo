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
 * Целые числа.
 */
class Tint extends Type
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function convert($var)
    {
        if (is_scalar($var)) {
            return intval($var);
        }

        return $var;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        return is_int($var);
    }
}
