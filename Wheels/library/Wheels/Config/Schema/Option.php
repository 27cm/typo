<?php

namespace Wheels\Config\Schema;

use Wheels\Config\Schema\Option\Type;

use Wheels\Typo\Module;
use Wheels\Typo\Exception;

/**
 * Класс описания параметра настроек.
 */
class Option
{
    /**
     * Имя параметра.
     *
     * @var string
     */
    protected $_name;

    /**
     * Тип параметра.
     *
     * @var \Wheels\Config\Schema\Option\Type
     */
    protected $_type;

    /**
     * Значение параметра по умолчанию.
     *
     * @var mixed
     */
    protected $_default;

    /**
     * Текстовое описание параметра.
     *
     * @var string
     */
    protected $_desc;


    // --- Конструктор ---

    /**
     *
     * @param string $name
     * @param string $default
     * @param \Wheels\Config\Schema\Option\Type $type
     */
    public function __construct($name, $default, Type $type = NULL)
    {
        $this->setName($name);
        $this->setDefault($default);
        $this->setType($type);
    }

    public function setDesc($desc)
    {
        if(!is_string($desc))
            Module::throwException(Exception::E_RUNTIME, 'Текстовое описание параметра должно быть строкой');
        $this->_desc = $desc;
    }

    public function setName($name)
    {
        if(!is_string($name))
            Module::throwException(Exception::E_RUNTIME, 'Имя параметра должно быть строкой');
        $this->_name = $name;
    }

    public function setType($type)
    {
        $this->_type = (is_null($type) ? Type::create('mixed') : $type);
        if(isset($this->_default))
            $this->setDefault($this->_default);
    }

    public function setDefault($value)
    {
        if(isset($this->_type))
        {
            $value = $this->_type->convert($value);
            $this->_type->validate($value);
        }
        $this->_default = $value;
    }
}
