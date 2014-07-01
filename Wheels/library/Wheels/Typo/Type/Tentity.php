<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Typo
 * @subpackage Wheels\Typo\Type
 */

namespace Wheels\Typo\Type;

use Wheels\Config\Option\Type\Tstring;
use Wheels\Typo\Typo;

/**
 * HTML сущность.
 */
class Tentity extends Tstring
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        if (!array_key_exists($var, Typo::getChars('chr'))) {
            return false;
        }

        return parent::validate($var);
    }
}
