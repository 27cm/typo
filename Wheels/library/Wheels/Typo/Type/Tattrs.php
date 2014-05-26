<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Typo
 * @subpackage Wheels\Typo\Type
 */

namespace Wheels\Typo\Type;

use Wheels\Config\Option\Type;

/**
 * Массив атрибутов.
 */
class Tattrs extends Type
{

    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function convert($var)
    {
        if (in_array($var, array(null, false, 'none', 'NONE'), true)) {
            return array();
        }

        if (is_array($var)) {
            foreach ($var as $key => $attr) {
                if (in_array($var, array(null, false, 'none', 'NONE'), true)) {
                    $var[$key] = array();
                    $attr = array();
                }

                if (is_array($attr)) {
                    if (!array_key_exists('value', $attr)) {
                        $var[$key]['value'] = '';
                    }
                    if (!array_key_exists('name', $attr)) {
                        $var[$key]['name'] = (string) $key;
                    }
                    if (!array_key_exists('cond', $attr)) {
                        $var[$key]['cond'] = true;
                    }
                }
            }
        }

        return $var;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        if (!is_array($var)) {
            return false;
        }

        foreach ($var as $attr) {
            if (!is_array($attr)) {
                return false;
            }
            if (!array_key_exists('value', $attr) || !array_key_exists('name', $attr) || !array_key_exists('cond', $attr)) {
                return false;
            }
        }

        return true;
    }
}
