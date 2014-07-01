<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option
 */

namespace Wheels\Typo\Module;

use Wheels\Typo\ITypo;

/**
 * Коллекция модулей типографа.
 *
 * @method Module[] getArray()
 * @method Module offsetGet($offset)
 */
class Collection extends \Wheels\Datastructure\Collection implements ITypo
{
    /**
     * Массив модулей типографа.
     *
     * @var \Wheels\Typo\Module\Module[]
     */
    protected $_array;


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function __construct(array $array = array())
    {
        parent::__construct('Wheels\Typo\Module\Module', $array);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareOffset($offset)
    {
        if (is_string($offset)) {
            $offset = Module::getModuleClassname($offset);
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

    public function getOptions()
    {
        $options = array();
        foreach ($this->getArray() as $name => $module) {
            $options[$name] = $module->getOptions();
        }
        return $options;
    }

    public function setOptions(array $options)
    {
        foreach ($options as $name => $moduleOptions) {
            $this->offsetGet($name)->setOptions($moduleOptions);
        }
        return $options;
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
    public function setOptionsFromGroup($name)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
