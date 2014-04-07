<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 */

namespace Wheels\Config;

use Wheels\Config\Schema\Option;
use Wheels\Config\Schema\Exception;

/**
 * Класс описаний параметров настроек.
 */
class Schema implements Countable, Iterator
{
    /**
     * Whether in-memory modifications to configuration data are allowed
     *
     * @var boolean
     */
    protected $_allowModifications = TRUE;

    /**
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element
     *
     * @var boolean
     */
    protected $_skipNextIteration = FALSE;

    /**
     * Регистрозависимость имён параметров.
     *
     * Содержит TRUE, если имена параметров зависят от регистра, и FALSE - в противном случае.
     * По умолчанию все имена параметров являются регистрозависимыми.
     *
     * @var bool
     */
    protected $_case_sensitive;

    /**
     * Описания параметров настроек.
     *
     * Ассоциативный массив с именами параметров в качестве ключей.
     *
     * @var \Wheels\Config\Schema\Option[]
     */
    protected $_options = array();

    protected $_index = 0;

    protected $_count = 0;


    // --- Конструктор ---

    public function __construct(array $options = array(), $caseSensitive = TRUE)
    {
        $this->_case_sensitive = $caseSensitive;
        $this->setOptions($options);
    }



    /**
     * Возвращает регистрозависимость имён параметров.
     *
     * @return bool Возвращает TRUE, если имена параметров зависят от регистра, и FALSE - в противном случае.
     */
    public function getCaseSensitive()
    {
        return $this->_case_sensitive;
    }

    /**
     * Возвращает описания параметров настроек.
     *
     * @return \Wheels\Config\Schema\Option[]
     */
    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($name)
    {
        $name = $this->prepareOptionName($name);

        if(!array_key_exists($name, $this->_options))
            throw new Exception("Неизвестный параметр '$name'");

        return $this->_options[$name];
    }

    /**
     * Задаёт описания параметров настроек.
     *
     * @param \Wheels\Config\Schema\Option[] $options Описания параметров настроек.
     */
    public function setOptions(array $options)
    {
        $this->_options = array();
        $this->addOptions($options);
    }

    /**
     * Добавляет описания параметров настроек.
     *
     * @param array $options Описания параметров настроек.
     */
    public function addOptions(array $options)
    {
        foreach($options as $option)
            $this->addOption($option);
    }

//    protected function _prepareOption(Option $option)
//    {
//        $name = $option->getName();
//        $name = $this->prepareOptionName($name);
//        $option->setName($name);
//    }

    public function addOption(Option $option)
    {
        // $this->prepareOption($option);
        $name = $option->getName();
        $name = $this->prepareOptionName($name);

        if(array_key_exists($name, $this->_options))
        {
            throw new Exception(
                "Описание параметра '$name' уже задано и для него не может быть добавлено ещё одно описание"
            );
        }

        $this->_options[$name] = $option;
    }

    public function prepareOptionName($name)
    {
        return ($this->getCaseSensitive() ? $name : mb_strtolower($name));
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     *
     * @return \Wheels\Config\Schema\Option
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if($this->_allowModifications)
        {
            $this->setOptions($name, $value);

            if(is_array($value))
            {
                $this->_data[$name] = new self($value, true);
            }
            else
            {
                $this->_data[$name] = $value;
            }
            $this->_count = count($this->_data);
        }
        else
            throw new Zend_Config_Exception('Zend_Config is read only');
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_options[$name]);
    }

    /**
     * Support unset() overloading on PHP 5.1
     *
     * @param  string $name
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __unset($name)
    {
        if ($this->_allowModifications)
        {
            unset($this->_options[$name]);
            $this->_count = count($this->_options);
            $this->_skipNextIteration = true;
        }
        else
            throw new Exception('Zend_Config is read only');
    }

    public function count()
    {
        return $this->_count;
    }

    public function rewind()
    {
        reset($this->_options);
        $this->_index = 0;
    }

    public function current()
    {
        return current($this->_options);
    }

    public function key()
    {
        return key($this->_options);
    }

    public function next()
    {
        next($this->_options);
        $this->_index++;
    }

    public function valid()
    {
        return $this->_index < $this->_count;
    }

    static public function create(array $schema)
    {
        $diff = array_diff(array_keys($schema), array('options', 'case-sensitive'));
        if(!empty($diff))
            throw new Exception('Неизвестные разделы описания конфигурации: ' . implode(', ', $diff));

        $options = array();
        if(array_key_exists('options', $schema))
        {
            if(!is_array($schema['options']))
                throw new Exception("Раздел 'options' описания конфигурации должен быть массивом");

            foreach($schema['options'] as $name => $option_schema)
            {
                if(!is_array($option_schema))
                    throw new Exception('Описание параметра настроек должно быть массивом');

                if(!array_key_exists('name', $option_schema))
                    $option_schema['name'] = $name;

                $options[] = Option::create($option_schema);
            }
        }

        if(array_key_exists('case-sensitive', $schema))
        {
            $caseSensitive = $schema['case-sensitive'];
            $schema = new self($options, $caseSensitive);
        }
        else
            $schema = new self($options);

        return $schema;
    }
}
