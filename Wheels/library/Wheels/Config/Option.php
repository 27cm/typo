<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 */

namespace Wheels\Config;

use Wheels\Config\Option\Type;
use Wheels\Config\Option\Exception;

use Wheels\Config\Option\OptionInterface;
use Wheels\IAllowModifications;

/**
 * Параметр.
 */
class Option implements OptionInterface, IAllowModifications
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

    /**
     * Обработчики событий.
     *
     * @var array
     */
    protected $_listeners = array(
        'setValue' => array(),
    );

    /**
     * Разрешение изменять параметр.
     *
     * Содержит true, если разрешено изменять свойства параметра, и false - в противном случае.
     * По умолчанию изменение параметра разрешено.
     *
     * @var bool
     */
    protected $_allowModifications = true;


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
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * {@inheritDoc}
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowed()
    {
        return $this->_allowed;
    }

    /**
     * Возвращает разрешение изменять массив.
     *
     * @return bool Возвращает true, если разрешено добавлять, изменять и удалять
     *              элементы массива, и false - в противном случае.
     */
    public function getAllowModifications()
    {
        return $this->_allowModifications;
    }

    /**
     * Устанавливает разрешение изменять массив.
     *
     * @param bool $value true, если необходимо разрешить добавлять, изменять и удалять
     *                    элементы массива, и false - в противном случае.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setAllowModifications($value)
    {
        $this->_allowModifications = (bool) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->_ensureAllowModification();

        if (!is_string($name)) {
            throw new Exception('Имя параметра должно быть строкой');
        }

        $this->_name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->_ensureAllowModification();

        $value = $this->_filter($value);

        if (!$this->validate($value)) {
            throw new Exception("Недопустимое значение параметра '" . $this->getName() . "'");
        }

        $this->_value = $value;
        $this->_on(__FUNCTION__, array($value));
    }

    /**
     * {@inheritDoc}
     */
    public function setValueDefault()
    {
        $this->_ensureAllowModification();

        $this->setValue($this->getDefault());
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault($value)
    {
        $this->_ensureAllowModification();

        $value = $this->_filter($value);

        if (!$this->validate($value)) {
            throw new Exception("Недопустимое значение по умолчанию параметра '" . $this->getName() . "'");
        }

        $this->_default = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setDesc($desc)
    {
        $this->_ensureAllowModification();

        if (!is_string($desc)) {
            throw new Exception("Текстовое описание параметра '" . $this->getName() . "' должно быть строкой");
        }

        $this->_desc = $desc;
    }

    /**
     * {@inheritDoc}
     */
    public function setAliases(array $aliases)
    {
        $this->_ensureAllowModification();

        foreach ($aliases as $value) {
            if (!$this->getType()->validate($value) || !$this->_isAllowed($value)) {
                throw new Exception("Недопустимое значение в массиве псевдонимов параметра '" . $this->getName() . "'");
            }
        }

        $save = $this->_aliases;
        $this->_aliases = $aliases;

        try {
            $this->setValue($this->getValue());
            $this->setDefault($this->getDefault());
        } catch (Exception $e) {
            $this->_aliases = $save;
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowed(array $allowed)
    {
        $this->_ensureAllowModification();

        $allowed = array_values($allowed);

        foreach ($allowed as $value) {
            if (!$this->getType()->validate($value)) {
                $name = $this->getName();
                throw new Exception("Недопустимое значение в массиве допустимых значений параметра '{$name}'");
            }
        }

        $save = $this->_allowed;
        $this->_allowed = $allowed;

        try {
            foreach ($this->getAliases() as $value) {
                if (!$this->_isAllowed($value)) {
                    $name = $this->getName();
                    throw new Exception("Недопустимое значение в массиве псевдонимов параметра '{$name}'");
                }
            }

            $this->setValue($this->getValue());
            $this->setDefault($this->getDefault());
        } catch (Exception $e) {
            $this->_allowed = $save;
            throw $e;
        }
    }

    /**
     * Добавляет обработчик события.
     *
     * @param string   $event    Название события.
     * @param callable $function Вызываемая функция.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function addEventListener($event, $function)
    {
        $this->_ensureAllowModification();
        $this->_ensureHasEvent($event);

        if (!is_callable($function)) {
            $name = $this->getName();
            throw new Exception("Обработчик события '{$event}' параметра '{$name}' должен иметь тип callable");
        }

        $this->_listeners[$event][] = $function;
    }

    /**
     * {@inheritDoc}
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
        $diff = array_diff(array_keys($schema), array('name', 'default', 'type', 'desc', 'aliases', 'allowed', 'allow-modification'));
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

        if (array_key_exists('allow-modification', $schema)) {
            $option->setAllowModifications($schema['allow-modification']);
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
        return (string) $this->getValue();
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
        if ((is_string($value) || is_int($value)) && array_key_exists($value, $this->_aliases)) {
            $value = $this->_aliases[$value];
        }

        $value = $this->getType()->convert($value);

        if ((is_string($value) || is_int($value)) && array_key_exists($value, $this->_aliases)) {
            $value = $this->_aliases[$value];
        }

        return $value;
    }

    /**
     * Проверяет, присутствует ли значение параметра в массиве допустимых значений.
     *
     * @param mixed $value Значение параметра.
     *
     * @return bool True, если значение параметра присутствует в массиве допустимых значений
     *              или массив допустимых значений пуст, и false - в противном случае.
     */
    protected function _isAllowed($value)
    {
        return (empty($this->_allowed) || array_search($value, $this->_allowed, true) !== false);
    }

    /**
     * Проверяет разрешение изменять параметр.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    protected function _ensureAllowModification()
    {
        if (!$this->getAllowModifications()) {
            $name = $this->getName();
            throw new Exception("Изменение параметра '{$name}' запрещено");
        }
    }

    /**
     * Вызывает все обработчики события с заданным именем.
     *
     * @param string $event     Название события.
     * @param array  $arguments Массив значений параметров, передаваемых обработчикам события.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    protected function _on($event, array $arguments = array())
    {
        $this->_ensureHasEvent($event);

        foreach ($this->_listeners[$event] as $function) {
            call_user_func_array($function, $arguments);
        }
    }

    /**
     * Проверяет существование события.
     *
     * @param string $event Название события.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    protected function _ensureHasEvent($event)
    {
        if (!array_key_exists($event, $this->_listeners)) {
            $name = $this->getName();
            throw new Exception("Неизвестное событие '{$event}' параметра '{$name}'");
        }
    }
}
