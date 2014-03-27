<?php

namespace Wheels\Config\Schema;

use Wheels\Config\Schema\Option\Exception;
use Wheels\Config\Schema\Option\Type;

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
     * @var string|NULL
     */
    protected $_desc = NULL;

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

        if(is_null($type))
            $this->_type = Type::create('mixed');
        else
            $this->_type = $type;

        $this->setDefault($default);
    }


    // --- Открытые методы ---

    /**
     * Возвращает имя параметра.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Возвращает имя параметра.
     *
     * @return \Wheels\Config\Schema\Option\Type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Возвращает значение параметра по умолчанию.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * Возвращает текстовое описание параметра.
     *
     * @return string|NULL
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * Возвращает ассоциативный массив псевдонимов.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * Возвращает массив допустимых значений.
     *
     * @return array
     */
    public function getAllowed()
    {
        return $this->_allowed;
    }

    /**
     * Задаёт имя параметра.
     *
     * @param string $name Имя параметра.
     */
    public function setName($name)
    {
        if(!is_string($name))
            throw new Exception('Имя параметра должно быть строкой');
        $this->_name = $name;
    }

    /**
     * Задаёт тип параметра.
     *
     * @param \Wheels\Config\Schema\Option\Type $type Тип параметра.
     */
    public function setType(Type $type)
    {
        $this->_type = $type;
        $this->setDefault($this->_default);
    }

    /**
     * Задаёт значение параметра по умолчанию.
     *
     * @param mixed $value Значение параметра по умолчанию.
     */
    public function setDefault($value)
    {
        if(!$this->validate($value))
            throw new Exception("Недопустимое значение по умолчанию параметра '" . $this->getName() . "'");

        $this->_default = $value;
    }

    /**
     * Задаёт текстовое описание параметра.
     *
     * @param string $desc Текстовое описание параметра.
     */
    public function setDesc($desc)
    {
        if(!is_string($desc))
            throw new Exception("Текстовое описание параметра '" . $this->getName() . "' должно быть строкой");
        $this->_desc = $desc;
    }

    /**
     * Задаёт массив псевдонимов.
     *
     * @param array $aliases Ассоциативный массив псевдонимов
     */
    public function setAliases(array $aliases)
    {
        $this->_aliases = $aliases;
    }

    /**
     * Задаёт массив допустимых значений.
     *
     * @param array $allowed Массив допустимых значений.
     */
    public function setAllowed(array $allowed)
    {
        $this->_allowed = array_values($allowed);
    }

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function validate($value)
    {
        if(array_key_exists($value, $this->_aliases, TRUE))
            $value = $this->_aliases[$value];

        // $value = $this->_type->convert($value);
        if(!$this->_type->validate($value))
            return FALSE;

        if(!empty($this->_allowed) && array_search($value, $this->_allowed, TRUE) !== FALSE)
            return FALSE;

        return TRUE;
    }
}
