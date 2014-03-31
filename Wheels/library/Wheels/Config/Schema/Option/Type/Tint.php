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
 * Целые числа.
 */
class Tint extends Type
{

    // --- Открытые методы ---

    /**
     * {@inheritdoc}
     */
    public function validate($var)
    {
        return is_int($var);
    }
}
