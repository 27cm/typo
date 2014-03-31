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
 * Тип переменной без значения.
 */
class Tnull extends Type
{
    
    // --- Открытые методы ---

    /**
     * {@inheritdoc}
     */
    public function validate($var)
    {
        return is_null($var);
    }
}
