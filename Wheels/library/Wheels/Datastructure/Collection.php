<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Datastructure
 */

namespace Wheels\Datastructure;

use Wheels\Datastructure\ArrayIterator;
use Wheels\Datastructure\Exception;

class Collection extends ArrayIterator
{
    protected $_elemsClass;

    // --- Конструктор ---

    /**
     * Создаёт коллекцию объектов заданного класса
     *
     * @param string $elemsClass Имя класса элементов коллекции.
     * @param array  $array      Массив элементов.
     */
    public function __construct($elemsClass = 'stdClass', array $array = array())
    {
        $this->_elemsClass = $elemsClass;
        parent::__construct($array);
    }


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if(!is_object($value) || !($value instanceof $this->_elemsClass))
            throw new Exception("В коллекцию можно добавлять только объекты класса {$this->_elemsClass}");
        else
            parent::offsetSet($offset, $value);
    }
}
