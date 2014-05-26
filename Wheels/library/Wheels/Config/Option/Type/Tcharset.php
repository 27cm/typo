<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Typo
 * @subpackage Wheels\Typo\Type
 */

namespace Wheels\Config\Option\Type;

/**
 * Кодировка.
 */
class Tcharset extends Tstring
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function convert($var)
    {
        if (is_string($var)) {
            $var = mb_strtoupper($var);
        }

        return parent::convert($var);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        return (parent::validate($var) && (iconv($var, 'UTF-8', '') !== false));
    }
}
