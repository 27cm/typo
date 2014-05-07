<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option
 */

namespace Wheels\Config\Option;

use Wheels\Config\Option;
use Wheels\Config\Option\Exception;

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


    // --- Открытые методы ---

    /**
     * Создаёт коллекцию параметров.
     *
     * @param \Wheels\Config\Option[] $array         Массив параметров.
     * @param bool                    $caseSensitive Регистрозависимость имён параметров.
     */
    public function __construct(array $array = array(), $caseSensitive = true)
    {
        $this->_caseSensitive = (bool)$caseSensitive;
        parent::__construct('Wheels\Config\Option', $array);
    }

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
     * @param string $offset Смещение (ключ).
     *
     * @return string Если ключи не зависят от регистра, то метод вернёт offset в нижнем регистре,
     *                в противном случае будет возвращено исходное значение offset.
     */
    public function prepareOffset($offset)
    {
        if (is_string($offset) && !$this->getCaseSensitive()) {
            return mb_strtolower($offset);
        } else {
            return $offset;
        }
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
        if (!is_string($offset) && $this->_checkElemClass($value)) {
            $offset = $value->getName();
        }

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
