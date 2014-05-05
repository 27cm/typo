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
 * Логический тип.
 */
class Tbool extends Type
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function convert($var)
    {
        if(in_array($var, array(1, '1', 'on', 'ON', 'true', 'TRUE'), true))
            return true;

        if(in_array($var, array(0, '0', 'off', 'OFF', 'false', 'FALSE'), true))
            return false;

        return $var;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        return is_bool($var);
    }
}
