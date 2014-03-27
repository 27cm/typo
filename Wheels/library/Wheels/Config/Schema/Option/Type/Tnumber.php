<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;
use Wheels\Config\Schema\Option\Type\Tint;
use Wheels\Config\Schema\Option\Type\Tfloat;

/**
 * Число типа Tint либо Tfloat.
 */
class Tnumber extends Type
{
    /**
     * Целочисленный тип.
     *
     * @var \Wheels\Config\Schema\Option\Type\Tint
     */
    static protected $_int;

    /**
     * Число с плавающей точкой.
     *
     * @var \Wheels\Config\Schema\Option\Type\Tfloat
     */
    static protected $_float;


    // --- Открытые методы ---

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    public function validate($var)
    {
        static::_init();
        return (static::$_int->validate($var) || static::$_float->validate($var));
    }


    // --- Защищенные методы ---

    static protected function _init()
    {
        if(is_null(static::$_int) || is_null(static::$_float))
        {
            static::$_int = new Tint();
            static::$_float = new Tfloat();
        }
    }
}
