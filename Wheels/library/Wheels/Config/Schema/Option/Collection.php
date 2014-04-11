<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Schema\Option
 */

namespace Wheels\Config\Schema\Option;

use Wheels\Config\Schema\Option;
use Wheels\Config\Schema\Option\Exception;

/**
 * Коллекция описаний параметров.
 */
class Collection extends \Wheels\Datastructure\Collection 
{
    /**
     * Регистрозависимость ключей.
     *
     * Содержит TRUE, если ключи зависят от регистра, и FALSE - в противном случае.
     * По умолчанию все ключи являются регистрозависимыми.
     *
     * @var bool
     */
    protected $_caseSensitive;


    // --- Конструктор ---

    /**
     * Создаёт объект описания конфигурации.
     *
     * @param array $array         Массив описаний параметров.
     * @param bool  $caseSensitive Регистрозависимость имён параметров.
     */
    public function __construct(array $array = array(), $caseSensitive = TRUE)
    {
        $this->_caseSensitive = (bool) $caseSensitive;
        parent::__construct('Wheels\Config\Schema\Option', $array);
    }


    // --- Открытые методы ---

     /**
     * Возвращает регистрозависимость имён параметров.
     *
     * @return bool Возвращает TRUE, если имена параметров зависят от регистра, и FALSE - в противном случае.
     */
    public function getCaseSensitive()
    {
        return $this->_caseSensitive;
    }

    /**
     * Подготовка смещения с учётом зависимости от регистра.
     *
     * @param scalar $offset Смещение (ключ).
     *
     * @return scalar Если ключи не зависят от регистра, то метод вернёт offset в нижнем регистре,
     *                в противном случае будет возвращено исходное значение offset.
     */
    public function prepareOffset($offset)
    {
        if(is_string($offset) && !$this->getCaseSensitive())
            return mb_strtolower($offset);
        else
            return $offset;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        $offset = $this->prepareOffset($offset);
        return parent::offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        $offset = $this->prepareOffset($offset);
        return parent::offsetGet($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if(!is_string($offset) && $this->_checkElemClass($value))
            $offset = $value->getName();

        $offset = $this->prepareOffset($offset);
        parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $offset = $this->prepareOffset($offset);
        parent::offsetUnset($offset);
    }
}
