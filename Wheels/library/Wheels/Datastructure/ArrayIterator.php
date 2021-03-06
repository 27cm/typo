<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Datastructure
 */

namespace Wheels\Datastructure;

use Iterator;
use ArrayAccess;
use Countable;
use Serializable;

use Wheels\IAllowModifications;

class ArrayIterator implements Iterator, ArrayAccess, Countable, Serializable, IAllowModifications
{
    /**
     * Массив элементов.
     *
     * @var array
     */
    protected $_array;

    /**
     * Число элементов массива.
     *
     * @var int
     */
    protected $_count;

    /**
     * Позиция внутреннего указателя.
     *
     * @var int
     */
    protected $_position;

    /**
     * Разрешение изменять массив.
     *
     * Содержит TRUE, если разрешено добавлять, изменять и удалять элементы массива, и false - в противном случае.
     * По умолчанию изменение массива разрешено.
     *
     * @var bool
     */
    protected $_allowModifications = true;


    // --- Открытые методы ---

    /**
     * Конструктор.
     *
     * @param array $array Массив элементов.
     */
    public function __construct(array $array = array())
    {
        $this->setArray($array);
    }

    /**
     * Возвращает копию массива.
     *
     * @return array Копия массива.
     */
    public function getArray()
    {
        return $this->_array;
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
     * Устанавливает массив.
     *
     * @param array $array Новый массив.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setArray(array $array)
    {
        $this->clear();
        foreach ($array as $key => $value) {
            $this->offsetSet($key, $value);
        }
        $this->rewind();
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
        foreach ($this->getArray() as $obj)
        {
            if (is_object($obj) && $obj instanceof IAllowModifications) {
                $obj->setAllowModifications($value);
            }
        }

        $this->_allowModifications = (bool) $value;
    }

    /**
     * Очищает внутренний массив.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function clear()
    {
        $this->_ensureAllowModification();

        $this->_array = array();
        $this->_count = 0;
        $this->rewind();
    }

    /**
     * Подготовка смещения.
     *
     * @param string $offset Смещение (ключ).
     *
     * @return mixed Подготовленное смещение.
     */
    public function prepareOffset($offset)
    {
        return $offset;
    }

    /**
     * Определяет, существует ли заданное смещение (ключ).
     *
     * Данный метод исполняется, когда используется функция isset() или empty().
     * Когда используется функция empty(), метод ArrayAccess::offsetGet() вызывается и
     * результат проверяется только в случае, если метод ArrayAccess::offsetExists() возвращает true.
     *
     * @param mixed $offset Смещение (ключ) для проверки.
     *
     * @return bool Возвращает true в случае успешного завершения или false в случае возникновения ошибки.
     */
    public function offsetExists($offset)
    {
        $offset = $this->prepareOffset($offset);
        return isset($this->_array[$offset]);
    }

    /**
     * Возвращает заданное смещение (ключ).
     *
     * Данный метод исполняется, когда проверяется смещение (ключ) на пустоту с помощью функции empty().
     *
     * @param mixed $offset Смещение (ключ) для возврата.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $offset = $this->prepareOffset($offset);
        return $this->offsetExists($offset) ? $this->_array[$offset] : null;
    }

    /**
     * Присваивает значение указанному смещению (ключу).
     *
     * @param mixed $offset Смещение (ключ), которому будет присваиваться значение.
     * @param mixed $value  Значение для присвоения.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function offsetSet($offset, $value)
    {
        $this->_ensureAllowModification();
        $offset = $this->prepareOffset($offset);

        if (is_null($offset)) {
            array_push($this->_array, $value);
            $this->_count++;
        } else {
            if (!$this->offsetExists($offset)) {
                $this->_count++;
            }
            $this->_array[$offset] = $value;
        }
    }

    /**
     * Удаляет смещение.
     *
     * @param mixed $offset Смещение для удаления.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function offsetUnset($offset)
    {
        $this->_ensureAllowModification();
        $offset = $this->prepareOffset($offset);

        if ($this->offsetExists($offset)) {
            unset($this->_array[$offset]);
            $this->_count--;
        }
    }

    /**
     * Возвращает текущий элемент массива.
     *
     * @return mixed Возвращает значение элемента массива, на который в данный момент указывает внутренний
     *               указатель массива. Сам указатель не изменяется. Если внутренний указатель
     *               указывает вне границ массива или массив пуст, метод возвращает false.
     */
    public function current()
    {
        return current($this->_array);
    }

    /**
     * Возвращает ключ текущего элемента массива.
     *
     * @return mixed Возвращает ключ элемента массива, на который в данный момент указывает внутренний
     *                указатель массива. Сам указатель не изменяется. Если внутренний указатель
     *                указывает вне границ массива или массив пуст, метод возвратит NULL.
     */
    public function key()
    {
        return key($this->_array);
    }

    /**
     * Устанавливает внутренний указатель массива на его первый элемент.
     *
     * В начале цикла foreach этот метод вызывается первым.
     * Этот метод не будет вызван после цикла foreach.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function rewind()
    {
        reset($this->_array);
        $this->_position = 0;
    }

    /**
     * Передвигает внутренний указатель массива на одну позицию вперёд.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function next()
    {
        next($this->_array);
        $this->_position++;
    }

    /**
     * Проверка корректности позиции внутреннего указателя.
     *
     * @return bool Если внутренний указатель указывает вне границ массива или массив пуст,
     *              метод возвращает false, в противном случае - true.
     */
    public function valid()
    {
        return $this->_position < $this->_count;
    }

    /**
     * Возвращает количество элементов массива.
     *
     * Перехватывает результат функции count().
     *
     * @return int Количество элементов в массиве.
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Представляет объект в виде строки.
     *
     * @return string Возращает строковое представление объекта.
     */
    public function serialize()
    {
        return serialize($this->getArray());
    }

    /**
     * Вызывается во время десериализации объекта.
     *
     * @param string $serialized Строковое представление объекта.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function unserialize($serialized)
    {
        $this->setArray(unserialize($serialized));
    }

    public function uasort($cmp_function)
    {
        uasort($this->_array, $cmp_function);
    }


    // --- Защищённые методы ---

    /**
     * @throws \Wheels\Datastructure\Exception
     */
    protected function _ensureAllowModification()
    {
        if (!$this->getAllowModifications()) {
            throw new Exception('Изменение структуры данных запрещено');
        }
    }
}
