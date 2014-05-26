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
use Wheels\Typo\Module\Module;

/**
 * Модуль типографа.
 */
class Tmodule extends Tstring
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function convert($var)
    {
        if (is_string($var)) {
            $var = Module::getModuleClassname($var);
        }

        return parent::convert($var);
    }
}
