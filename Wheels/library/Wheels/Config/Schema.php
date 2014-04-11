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
 * Класс описания конфигурации.
 */
class Schema
{
    /**
     * Коллекция описаний параметров.
     *
     * @var \Wheels\Config\Schema\Option\Collection
     */
    protected $_options;


    // --- Конструктор ---

    /**
     * Создаёт объект описания конфигурации.
     *
     * @param array $options       Массив описаний параметров.
     * @param bool  $caseSensitive Регистрозависимость имён параметров.
     */
    public function __construct(array $options = array(), $caseSensitive = TRUE)
    {
        $this->_options = new Collection($options, $caseSensitive);
    }

    /**
     * Возвращает коллекцию описаний параметров.
     *
     * @return \Wheels\Config\Schema\Option\Collection
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Возвращает описание параметра с заданным именем.
     *
     * @param string $name Имя параметра.
     *
     * @return \Wheels\Config\Schema\Option Описание параметра с заданным именем.
     *
     * @throws \Wheels\Config\Schema\Exception
     */
    public function getOption($name)
    {
        return $this->_options[$name];
    }

    /**
     * Задаёт описания параметров.
     *
     * @param \Wheels\Config\Schema\Option[] $options Описания параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptions(array $options)
    {
        $this->_options->clear();
        $this->addOptions($options);
    }

    /**
     * Добавляет описания параметров.
     *
     * @param \Wheels\Config\Schema\Option[] $options Описания параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function addOptions(array $options)
    {
        foreach($options as $option)
            $this->addOption($option);
    }

    /**
     * Добавляет описание параметра.
     *
     * @param \Wheels\Config\Schema\Option $option Описание параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function addOption(Option $option)
    {
        $this->_options[] = $option;
    }

    /**
     * Создаёт объект класса по его описанию.
     *
     * @param array $schema Ассоциативный массив с описанием конфигурации.
     *                       * allow-modifications - ассоциативный массив псевдонимов;
     *                       * options             - массив описаний параметров;
     *                       * case-sensitive      - текстовое описание параметра.
     *
     * @return \Wheels\Config\Schema
     *
     * @throws \Wheels\Config\Schema\Exception
     */
    static public function create(array $schema)
    {
        $diff = array_diff(array_keys($schema), array('options', 'case-sensitive', 'allow-modifications'));
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

                if(!array_key_exists('name', $option_schema) && is_string($name))
                    $option_schema['name'] = $name;

                $options[] = Option::create($option_schema);
            }
        }

        if(array_key_exists('case-sensitive', $schema))
        {
            $caseSensitive = $schema['case-sensitive'];
            $obj = new self($options, $caseSensitive);
        }
        else
            $obj = new self($options);

        if(array_key_exists('allow-modifications', $schema))
        {
            $value = $schema['allow-modifications'];
            $obj->getOptions()->setAllowModifications($value);
        }

        return $obj;
    }
}
