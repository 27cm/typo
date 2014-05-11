<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option
 */

namespace Wheels\Typo\Module;

use Wheels\Typo\TypoInterface;

/**
 * Коллекция модулей типографа.
 */
class Collection extends \Wheels\Datastructure\Collection implements TypoInterface
{
    /**
     * Массив модулей типографа.
     *
     * @var \Wheels\Typo\Module\AbstractModule[]
     */
    protected $_array;


    // --- Открытые методы ---

    /**
     * Создаёт коллекцию параметров.
     *
     * @param \Wheels\Typo\Module\AbstractModule[] $array Массив модулей типографа.
     */
    public function __construct(array $array = array())
    {
        parent::__construct('Wheels\Typo\Module\AbstractModule', $array);
    }

    /**
     * Возвращает копию массива модулей.
     *
     * @return \Wheels\Typo\Module\AbstractModule[] Копия массива модулей.
     */
    public function getArray()
    {
        return parent::getArray();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareOffset($offset)
    {
        if (is_string($offset)) {
            $offset = AbstractModule::getModuleClassname($offset);
        }

        return parent::prepareOffset($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset) && $this->_checkElemClass($value)) {
            $offset = get_class($value);
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowModifications($value)
    {
        foreach ($this->getArray() as $module) {
            $module->setAllowModifications($value);
        }

        parent::setAllowModifications($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setConfigDir($dir)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroup($name, $required = false)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names, $required = false)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
