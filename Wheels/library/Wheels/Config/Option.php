<?php

namespace Wheels\Config;

use Wheels\Config\Option\Type;
use Wheels\Config\Option\Exception;

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
     * Значение параметра.
     *
     * @var string
     */
    protected $_value;

    /**
     * Значение параметра по умолчанию.
     *
     * @var mixed
     */
    protected $_default;

    /**
     * Тип параметра.
     *
     * @var \Wheels\Config\Option\Type
     */
    protected $_type;

    /**
     * Текстовое описание параметра.
     *
     * @var string|NULL
     */
    protected $_desc = null;

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


    // --- Открытые методы ---

    /**
     *
     * @param string $name    Имя параметра.
     * @param string $default Значение параметра по умолчанию.
     * @param mixed  $type    Тип параметра (не может быть изменён после создания объекта). По умолчанию mixed.
     *
     * @uses \Wheels\Config\Option\Type::create()
     */
    public function __construct($name, $default, $type = null)
    {
        $this->setName($name);

        if ($type instanceof Type) {
            $this->_type = $type;
        } elseif (is_string($type)) {
            $this->_type = Type::create($type);
        } else {
            $this->_type = Type::create('mixed');
        }

        $this->setDefault($default);
        $this->setValueDefault();
    }

    /**
     * Возвращает имя параметра.
     *
     * @return string Имя параметра.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Возвращает значение параметра.
     *
     * @return mixed Значение параметра.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Возвращает значение параметра по умолчанию.
     *
     * @return mixed Значение параметра по умолчанию.
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * Возвращает тип параметра.
     *
     * @return \Wheels\Config\Option\Type Тип параметра.
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Возвращает текстовое описание параметра.
     *
     * @return string|null Текстовое описание параметра или NULL, если описание не было задано.
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * Возвращает ассоциативный массив псевдонимов.
     *
     * @return array Ассоциативный массив псевдонимов.
     */
    public function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * Возвращает массив допустимых значений.
     *
     * @return array Массив допустимых значений.
     */
    public function getAllowed()
    {
        return $this->_allowed;
    }

    /**
     * Задаёт имя параметра.
     *
     * @param string $name Имя параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new Exception('Имя параметра должно быть строкой');
        }

        $this->_name = $name;
    }

    /**
     * Задаёт значение параметра.
     *
     * @param mixed $value Значение параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setValue($value)
    {
        $value = $this->_filter($value);

        if (!$this->validate($value)) {
            throw new Exception("Недопустимое значение параметра '" . $this->getName() . "'");
        }

        $this->_value = $value;
    }

    /**
     * Устанавливает значение параметра по умолчанию в качестве текущего значения параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setValueDefault()
    {
        $this->setValue($this->getDefault());
    }

    /**
     * Задаёт значение параметра по умолчанию.
     *
     * @param mixed $value Значение параметра по умолчанию.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setDefault($value)
    {
        $value = $this->_filter($value);

        if (!$this->validate($value)) {
            throw new Exception("Недопустимое значение по умолчанию параметра '" . $this->getName() . "'");
        }

        $this->_default = $value;
    }

    /**
     * Задаёт текстовое описание параметра.
     *
     * @param string $desc Текстовое описание параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setDesc($desc)
    {
        if (!is_string($desc)) {
            throw new Exception("Текстовое описание параметра '" . $this->getName() . "' должно быть строкой");
        }

        $this->_desc = $desc;
    }

    /**
     * Задаёт массив псевдонимов.
     *
     * @param array $aliases Ассоциативный массив псевдонимов.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setAliases(array $aliases)
    {
        foreach ($aliases as $value) {
            if (!$this->getType()->validate($value) || !$this->_isAllowed($value)) {
                throw new Exception("Недопустимое значение в массиве псевдонимов параметра '" . $this->getName() . "'");
            }
        }

        $save = $this->_aliases;
        $this->_aliases = $aliases;

        try {
            $this->setDefault($this->getDefault());
        } catch (Exception $e) {
            $this->_aliases = $save;
            throw $e;
        }
    }

    /**
     * Задаёт массив допустимых значений.
     *
     * @param array $allowed Массив допустимых значений.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setAllowed(array $allowed)
    {
        $allowed = array_values($allowed);

        foreach ($allowed as $value) {
            if (!$this->getType()->validate($value)) {
                throw new Exception("Недопустимое значение в массиве допустимых значений параметра '" . $this->getName()
                    . "'");
            }
        }

        $save = $this->_allowed;
        $this->_allowed = $allowed;

        try {
            foreach ($this->getAliases() as $value) {
                if (!$this->_isAllowed($value)) {
                    throw new Exception("Недопустимое значение в массиве псевдонимов параметра '" . $this->getName()
                        . "'");
                }
            }

            $this->setDefault($this->getDefault());
        } catch (Exception $e) {
            $this->_allowed = $save;
            throw $e;
        }
    }

    /**
     * Проверяет значение параметра.
     *
     * @param mixed $value Значение параметра.
     *
     * @return bool Если значение параметра является допустимым, то метод возвращает TRUE, иначе - FALSE.
     */
    public function validate($value)
    {
        $value = $this->_filter($value);

        return ($this->getType()->validate($value) && $this->_isAllowed($value));
    }

    /**
     * Создаёт объект класса по его описанию.
     *
     * @param array $schema  Ассоциативный массив с описанием создаваемого параметра.
     *                       Обязательные ключи:
     *                       * name    - имя параметра;
     *                       * default - значение параметра по умолчанию.
     *                       Дополнительные ключи:
     *                       * type    - тип параметра;
     *                       * desc    - текстовое описание параметра;
     *                       * aliases - ассоциативный массив псевдонимов;
     *                       * allowed - массив допустимых значений.
     *
     * @throws \Wheels\Config\Option\Exception
     *
     * @return \Wheels\Config\Option Объект, соответствующий заданному описанию.
     */
    static public function create(array $schema)
    {
        $diff = array_diff(array_keys($schema), array('name', 'default', 'type', 'desc', 'aliases', 'allowed'));
        if (!empty($diff)) {
            throw new Exception('Неизвестные разделы описания параметра: ' . implode(', ', $diff));
        }

        if (!array_key_exists('name', $schema)) {
            throw new Exception('Не задано имя параметра');
        }

        if (!array_key_exists('default', $schema)) {
            throw new Exception('Не задано значение параметра по умолчанию');
        }

        $name = $schema['name'];
        $default = $schema['default'];

        if (array_key_exists('type', $schema)) {
            $type = $schema['type'];
            $option = new self($name, $default, $type);
        } else {
            $option = new self($name, $default);
        }

        if (array_key_exists('desc', $schema)) {
            $option->setDesc($schema['desc']);
        }

        if (array_key_exists('aliases', $schema)) {
            $option->setAliases($schema['aliases']);
        }

        if (array_key_exists('allowed', $schema)) {
            $option->setAllowed($schema['allowed']);
        }

        return $option;
    }

    /**
     * Преобразовывает объект в строку.
     *
     * @return string Значение параметра.
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }


    // --- Защищенные методы ---

    /**
     * Изменяет значение, если его можно преобразовать к заданному типу или для него задан псевдоним.
     *
     * @param mixed $value Значение параметра.
     *
     * @return mixed Псевдоним для value, если таковой имеется, в противном случае просто value.
     */
    protected function _filter($value)
    {
        $value = $this->getType()->convert($value);

        if ((is_integer($value) || is_string($value)) && array_key_exists($value, $this->_aliases)) {
            $value = $this->_aliases[$value];
        }

        return $value;
    }

    protected function _isAllowed($value)
    {
        return (empty($this->_allowed) || array_search($value, $this->_allowed, true) !== false);
    }
}
