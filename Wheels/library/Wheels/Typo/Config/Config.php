<?php

/**
 * Wheels Library
 *
 * @category Wheels
 * @package  Wheels\Typo
 */

namespace Wheels\Typo\Config;

use Wheels\Typo\Typo;
use Wheels\Config\Option;
use Wheels\Utility;

/**
 * {@inheritDoc}
 */
class Config extends \Wheels\Config\Config
{
    /**
     * Типограф, использующий данный конфиг.
     *
     * @var \Wheels\Typo\Typo
     */
    protected $_typo;


    // --- Открытые методы ---

    /**
     * Конструктор.
     *
     * @param \Wheels\Typo\Typo       $typo          Типограф, использующий данный конфиг.
     * @param \Wheels\Config\Option[] $options       Массив описаний параметров.
     * @param bool                    $caseSensitive Регистрозависимость имён параметров.
     */
    public function __construct(Typo $typo, array $options = array(), $caseSensitive = true)
    {
        $this->_typo = $typo;

        parent::__construct($options, $caseSensitive);
    }

    /**
     * Возвращает типограф, использующий данный конфиг.
     *
     * @return \Wheels\Typo\Typo Типограф, использующий данный конфиг.
     */
    public function getTypo()
    {
        return $this->_typo;
    }

    /**
     * {@inheritDoc}
     */
    public function getOption($name)
    {
        if (preg_match('~^(?:module\:\:)?([^\:]+)\:\:([^\:]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $optionName = $matches[2];
            return $this->getTypo()->getModule($moduleName)->getConfig()->getOption($optionName);
        } else {
            return parent::getOption($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionValue($name)
    {
        if (preg_match('~^module\:\:([^\:]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            return $this->getTypo()->getModule($moduleName)->getOptions();
        } elseif (preg_match('~^(?:module\:\:)?([^\:]+)\:\:([^\:]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $optionName = $matches[2];
            return $this->getTypo()->getModule($moduleName)->getOption($optionName);
        } else {
            return parent::getOptionValue($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsValues()
    {
        $keys = array();
        $modulesOptions = $this->getTypo()->getModules()->getOptions();
        foreach (array_keys($modulesOptions) as $name) {
            $keys[] = 'module::' . $name;
        }
        $modulesOptions = array_values($modulesOptions);
        $modulesOptions = array_combine($keys, $modulesOptions);

        $options = parent::getOptionsValues();

        return array_merge($options, $modulesOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionValue($name, $value)
    {
        if (preg_match('~^module\:\:([^\:]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $this->getTypo()->getModule($moduleName)->setOptions($value);
        } elseif (preg_match('~^(?:module\:\:)?([^\:]+)\:\:([^\:]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $optionName = $matches[2];
            $this->getTypo()->getModule($moduleName)->setOption($optionName, $value);
        } else {
            parent::setOptionValue($name, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsValues(array $options)
    {
        $o = $this->getOptions();

        foreach ($options as $name => $value) {
            // $this->optionEqual($name, 'modules');
            if ($o->prepareOffset($name) === $o->prepareOffset('modules')) {
                $this->setOptionValue($name, $value);
                unset($options[$name]);
                break;
            }
        }

        parent::setOptionsValues($options);
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowModifications($value)
    {
        $this->getTypo()->getModules()->setAllowModifications($value);
        parent::setAllowModifications($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsValuesDefault()
    {
        parent::setOptionsValuesDefault();
        $this->getTypo()->getModules()->setDefaultOptions();
    }
}
