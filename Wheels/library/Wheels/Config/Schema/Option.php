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

    /**
     * Ассоциативный массив псевдонимов.
     *
     * @var array
     */
    protected $_aliases = array();

    /**
     * Массив допустимых значений.
     *
     * @var array
     */
    protected $_allowed = array();


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


    // --- Открытые методы ---

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

    public function setAliases(array $aliases)
    {
        $this->_aliases = $aliases;
    }

    public function setAllowed(array $allowed)
    {
        $this->_allowed = $allowed;
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
            if(array_key_exists($value, $this->_aliases))
                $value = $this->_aliases[$value];

            $value = $this->_type->convert($value);
            if(!$this->_type->validate($value))
                Module::throwException(Exception::E_RUNTIME, "Значение параметра '{$this->_name}' имеет не верный тип");

            if(!empty($this->_allowed) && array_search($value, $this->_allowed, TRUE) !== FALSE)
                Module::throwException(Exception::E_RUNTIME, "Недопустимое значение параметра '{$this->_name}'");
        }
        $this->_default = $value;
    }

    public function getDefault()
    {
        return $this->_default;
    }
}
