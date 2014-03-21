<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Массив.
 */
class Tarray extends Type
{
    /**
     * Тип элементов массива.
     *
     * @var \Wheels\Config\Schema\Option\Type
     */
    protected $_type;

    /**
     * @see Wheels\Config\Schema\Option\Type::convert()
     */
    public function convert($var)
    {
        if(is_array($var))
        {
            foreach($var as &$value)
            {
                $value = $this->_type->convert($value);
            }
        }

        return $var;
    }

    /**
     * @see Wheels\Config\Schema\Option\Type::validate()
     */
    public function validate($var)
    {
        if(is_array($var))
        {
            foreach($var as $value)
            {
                if(!$this->_type->validate($value))
                    return FALSE;
            }
            return TRUE;
        }

        return FALSE;
    }
}
