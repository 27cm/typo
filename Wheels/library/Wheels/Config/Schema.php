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
use Wheels\Config\Schema\Option\Collection;

/**
 * Класс описаний параметров настроек.
 */
class Schema
{
    /**
     * Разрешение изменять массив описаний параметров.
     *
     * Содержит TRUE, если разрешено добавлять, изменять и удалять описания параметров, и FALSE - в противном случае.
     * По умолчанию изменение массива описаний параметров разрешено.
     *
     * @var bool
     */
    protected $_allowModifications = TRUE;

    /**
     * Используется при удалении описания параметра, чтобы быть уверенными, что не пропустим следующий элемент.
     *
     * @see \Wheels\Config\Schema::__unset()
     *
     * @var bool
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
    protected $_caseSensitive;

    /**
     * Описания параметров настроек.
     *
     * Ассоциативный массив с именами параметров в качестве ключей.
     *
     * @var \Wheels\Config\Schema\Option\Collection
     */
    protected $_options;

    protected $_index = 0;

    protected $_count = 0;


    // --- Конструктор ---

    public function __construct(array $options = array(), $caseSensitive = TRUE)
    {
        $this->_caseSensitive = $caseSensitive;
        $this->_options = new Collection();
        $this->setOptions($options);
    }

    public function setAllowModifications($value)
    {
        $this->_allowModifications = (bool) $value;
    }

    public function getAllowModifications()
    {
        return $this->_allowModifications;
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
     * Возвращает описания параметров настроек.
     *
     * @return \Wheels\Config\Schema\Option\ArrayOption
     */
    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($name)
    {
        $name = $this->prepareOptionName($name);

        if(!isset($this->_options[$name]))
            throw new Exception("Неизвестный параметр '$name'");

        return $this->_options[$name];
    }

    protected function _ensureAllowModifications()
    {
        if(!$this->getAllowModifications())
            throw new Exception('Изменение массива описаний параметров запрещено');
    }

    /**
     * Задаёт описания параметров настроек.
     *
     * @param \Wheels\Config\Schema\Option[] $options Описания параметров настроек.
     */
    public function setOptions(array $options)
    {
        $save = $this->_options;
        try
        {
            $this->getOptions()->clear();

            $this->addOptions($options);
        }
        catch(\Exception $e)
        {
            $this->_options = $save;
            throw $e;
        }
    }

    /**
     * Добавляет описания параметров настроек.
     *
     * @param array $options Описания параметров настроек.
     */
    public function addOptions(array $options)
    {
        $this->_ensureAllowModifications();

        foreach($options as $option)
            $this->addOption($option);
    }

    public function addOption(Option $option)
    {
        $this->_ensureAllowModifications();

        $name = $option->getName();
        $name = $this->prepareOptionName($name);

        $this->_options[$name] = $option;
        $this->_count = count($this->_data);
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
     * @param  mixed  $option
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __set($name, Option $option)
    {
        $this->_ensureAllowModifications();

        $option->setName($name);
        $this->addOption($option);
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $name = $this->prepareOptionName($name);

        return isset($this->_options[$name]);
    }

    /**
     * Support unset() overloading on PHP 5.1
     *
     * @param  string $name
     * @throws Zend_Config_Exception
     */
    public function __unset($name)
    {
        $this->_ensureAllowModifications();

        $name = $this->prepareOptionName($name);

        unset($this->_options[$name]);
        $this->_count = count($this->_options);
        $this->_skipNextIteration = true;
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
