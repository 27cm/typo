<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Datastructure
 */

namespace Wheels\Datastructure;

class Collection extends ArrayIterator
{
    /**
     * Класс элементов.
     *
     * @var string
     */
    protected $_elemsClass;


    // --- Открытые методы ---

    /**
     * Конструктор.
     *
     * @param string $elemsClass Имя класса элементов коллекции.
     * @param array  $array      Массив элементов.
     */
    public function __construct($elemsClass = 'stdClass', array $array = array())
    {
        $this->_elemsClass = $elemsClass;
        parent::__construct($array);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->_checkElemClass($value)) {
            throw new Exception("В коллекцию можно добавлять только объекты класса {$this->_elemsClass}");
        } else {
            parent::offsetSet($offset, $value);
        }
    }

    /**
     * Обрабатывает вызов недоступных методов.
     *
     * @param string $name      Имя вызываемого метода.
     * @param array  $arguments Массив параметров, переданных в вызываемый метод.
     *
     * @return self
     */
    public function __call($name, $arguments)
    {
        foreach ($this->_array as $obj) {
            call_user_func_array(array($obj, $name), $arguments);
        }

        return $this;
    }


    // --- Защищенные методы ---

    protected function _checkElemClass($value)
    {
        return (is_object($value) && ($value instanceof $this->_elemsClass));
    }
}
