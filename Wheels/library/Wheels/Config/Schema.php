<?php

namespace Wheels\Config;

use Wheels\Config\Schema\Option;
use Wheels\Config\Schema\Option\Type;

use Wheels\Typo\Module;
use Wheels\Typo\Exception;

/**
 * Класс описаний параметров настроек.
 */
class Schema
{
    /**
     * Регистрозависимость имени параметра.
     *
     * @var bool
     */
    protected $_case_sensitive = TRUE;

    /**
     * Описания параметров настроек.
     *
     * @var \Wheels\Config\Schema\Option[]
     */
    protected $_options = array();


    // --- Конструктор ---

    public function __construct(array $schema = NULL)
    {
        $diff = array_diff(array_keys($schema), array('options', 'case-sensitive'));
        if(!empty($diff))
            Module::throwException(Exception::E_RUNTIME, 'Неизвестные разделы описания конфигурации: ' . implode(', ', $diff));

        if(array_key_exists('case-sensitive', $schema))
            $this->_case_sensitive = (bool) $schema['case-sensitive'];

        if(array_key_exists('options', $schema))
        {
            if(!is_array($schema['options']))
                Module::throwException(Exception::E_RUNTIME, "Раздел 'options' описания конфигурации должен быть массивом");

            foreach($schema['options'] as $name => $option_schema)
            {
                if(!is_array($option_schema))
                    Module::throwException(Exception::E_RUNTIME, "Элементы раздела 'options' описания конфигурации должны быть массивами");

                $this->addOption($name, $option_schema);
            }
        }

        $this->_schema = $schema;
    }

    public function addOption($name, array $schema)
    {
        if(!array_key_exists('default', $schema))
            Module::throwException(Exception::E_RUNTIME, "Для параметра '$name' раздела 'options' описания конфигурации не задано значение по умолчанию");

        $default = $schema['default'];

        if(array_key_exists('type', $schema))
        {
            $type = Type::create($schema['type']);
            $option = new Option($name, $default, $type);
        }
        else
        {
            $option = new Option($name, $default);
        }

        if(array_key_exists('desc', $schema))
            $option->setDesc($schema['desc']);

        $this->_options[$name] = $option;
    }
}
