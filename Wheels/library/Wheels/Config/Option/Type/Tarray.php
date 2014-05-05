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
 * Массив элементов заданного типа.
 */
class Tarray extends Type
{
    /**
     * Тип элементов массива.
     *
     * @var \Wheels\Config\Option\Type
     */
    protected $_type;


    // --- Конструктор ---

    /**
     * Создание типа - массива элементов заданного типа.
     *
     * @param mixed $type Тип элементов массива. Если не задан, то используется тип {@link \Wheels\Config\Option\Type\Tmixed}.
     *
     * @uses \Wheels\Config\Option\Type::create()
     */
    public function __construct($type = NULL)
    {
        if($type instanceof Type)
            $this->_type = $type;
        elseif(is_null($type))
            $this->_type = static::create('mixed');
        else
            $this->_type = static::create($type);
    }


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function convert($var)
    {
        if(in_array($var, array(null, false, 'none', 'NONE'), true))
            return array();

        return $var;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        if(is_array($var))
        {
            foreach($var as $value)
            {
                if(!$this->_type->validate($value))
                    return false;
            }
            return true;
        }

        return false;
    }
}
