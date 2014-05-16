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

/**
 * Коллекция параметров.
 */
class Collection extends \Wheels\Datastructure\Collection implements OptionInterface
{
    /**
     * Массив параметров.
     *
     * @var \Wheels\Config\Option[]
     */
    protected $_array;

    /**
     * Регистрозависимость ключей.
     *
     * Содержит true, если ключи зависят от регистра, и false - в противном случае.
     * По умолчанию все ключи являются регистрозависимыми.
     *
     * @var bool
     */
    protected $_caseSensitive;


    // --- Открытые методы ---

    /**
     * Конструктор.
     *
     * @param \Wheels\Config\Option[] $array         Массив параметров.
     * @param bool                    $caseSensitive Регистрозависимость имён параметров.
     */
    public function __construct(array $array = array(), $caseSensitive = true)
    {
        $this->_caseSensitive = (bool) $caseSensitive;
        parent::__construct('Wheels\Config\Option', $array);
    }

    /**
     * Возвращает копию массива параметров.
     *
     * @return \Wheels\Config\Option[] Копия массива параметров.
     */
    public function getArray()
    {
        return parent::getArray();
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
     * {@inheritDoc}
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
    public function offsetSet($offset, $value)
    {
        /** @var \Wheels\Config\Option $value */

        if (!is_string($offset) && $this->_checkElemClass($value)) {
            $offset = $value->getName();
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        $values = array();

        foreach ($this->getArray() as $option) {
            $values[$option->getName()] = $option->getValue();
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getDesc()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowed()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setValueDefault()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault($value)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setDesc($desc)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setAliases(array $aliases)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowed(array $allowed)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
