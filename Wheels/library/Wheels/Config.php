<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 */

namespace Wheels;

use Wheels\Config\Option;
use Wheels\Config\Exception;
use Wheels\Config\Option\Collection;

/**
 * Класс для работы с параметрами настроек.
 *
 * Объекту этого класса может быть задано описание конфигурации,
 * включающее имена, типы, значения параметров по умолчанию и т.п.
 *
 * Параметры конфигурации могут быть установлены из ассоциативного массива,
 * конфигурационного INI файла, в том числе из отдельной секции INI файла.
 */
class Config
{
    /**
     * Параметры настроек.
     *
     * @var \Wheels\Config\Option\Collection|\Wheels\Config\Option[]
     */
    protected $_options;

    /**
     * Группы настроек.
     *
     * @var array
     */
    protected $_groups = array();


    // --- Константы ---

    /**
     * Разделитель ключей параметров.
     */
    const KEY_SEP = '.';

    /**
     * Разделитель секций.
     */
    const SECTION_SEP = ':';


    // --- Открытые методы ---

    /**
     * Конструктор.
     *
     * @param \Wheels\Config\Option[] $options       Массив описаний параметров.
     * @param bool                    $caseSensitive Регистрозависимость имён параметров.
     */
    public function __construct(array $options = array(), $caseSensitive = true)
    {
        $this->_options = new Collection($options, $caseSensitive);
    }

    /**
     * Возвращает параметры настроек.
     *
     * @return \Wheels\Config\Option\Collection|\Wheels\Config\Option[] Коллекция параметров настроек.
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Возвращает параметр с заданным именем.
     *
     * @param string $name Имя параметра.
     *
     * @return \Wheels\Config\Option Параметр с заданным именем.
     */
    public function getOption($name)
    {
        return $this->_options[$name];
    }

    /**
     * Возвращает значения параметров настроек.
     *
     * @return array Ассоциативный массив значений параметров.
     */
    public function getOptionsValues()
    {
        $optionsValues = array();

        foreach($this->getOptions() as $name => $option)
            $optionsValues[$name] = $option->getValue();

        return $optionsValues;
    }

    /**
     * Возвращает значение параметра.
     *
     * @param string $name Название параметра.
     *
     * @return mixed Значение параметра с заданным именем.
     */
    public function getOptionValue($name)
    {
        return $this->getOption($name)->getValue();
    }

    /**
     * Задаёт параметры.
     *
     * @param \Wheels\Config\Option[] $options Массив параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptions(array $options)
    {
        $this->getOptions()->clear();
        $this->addOptions($options);
    }

    /**
     * Добавляет параметр.
     *
     * @param \Wheels\Config\Option $option Описание параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function addOption(Option $option)
    {
        $this->_options[] = $option;
    }

    /**
     * Добавляет параметры.
     *
     * @param \Wheels\Config\Option[] $options Массив параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function addOptions(array $options)
    {
        foreach($options as $option)
            $this->addOption($option);
    }

    /**
     * Устанавливает значение параметра.
     *
     * @param string $name  Название параметра.
     * @param mixed  $value Значение параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionValue($name, $value)
    {
        $this->getOption($name)->setValue($value);
    }

    /**
     * Устанавливает значения параметров.
     *
     * @param array $options Ассоциативный массив значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionsValues(array $options)
    {
        foreach($options as $name => $value)
            $this->setOptionValue($name, $value);
    }

    public function getGroups()
    {
        return $this->_groups;
    }

    /**
     * Устанавливает группы значений параметров.
     *
     * @param array $groups Группы значений настроек.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setGroups(array $groups)
    {
        $this->_groups = array();
        $this->addGroups($groups);
    }



    public function addGroups(array $groups)
    {
        foreach ($groups as $name => $group) {
            $this->addGroup($name, $group);
        }
    }

    public function addGroup($name, array $group)
    {

    }

    public function getGroup($name)
    {
        if(!array_key_exists($name, $this->getGroups()))
            throw new Exception("Не");

        return $this->_groups[$name];
    }

    public function setOptionsValuesFromGroup($name)
    {
        $options = $this->getGroup($name);
        $this->setOptionsValues($options);
    }

    /**
     * Устанавливает значения параметров из конфигурационного INI файла.
     *
     * @param string $filename Имя обрабатываемого ini-файла.
     * @param string $section  Имя секции. По умолчанию значения параметров устанавливаются из секции 'default'.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Exception
     */
    public function setSectionsFromFile($filename, $section = 'default')
    {
        static $optionsFromFiles = array();
        
        if(!array_key_exists($filename, $optionsFromFiles))
            $optionsFromFiles[$filename] = $this->_processIniFile($filename);

        if(!array_key_exists($section, $optionsFromFiles[$filename]) || !is_array($optionsFromFiles[$filename][$section]))
            throw new Exception("Раздел настроек '$section' не найден в конфигурационном файле '$filename'");

        $options = $optionsFromFiles[$filename][$section];
        $this->setOptionsValues($options);
    }

    /**
     * Создаёт объект класса по его описанию.
     *
     * @param array $schema Ассоциативный массив с описанием конфигурации.
     *                       * allow-modifications - ассоциативный массив псевдонимов;
     *                       * options             - массив описаний параметров;
     *                       * groups              - массив групп значений параметров;
     *                       * case-sensitive      - регистрозависимость имён параметров.
     *
     * @return \Wheels\Config Конфигурация с заданным описанием.
     *
     * @throws \Wheels\Config\Exception
     */
    static public function create(array $schema)
    {
        $diff = array_diff(array_keys($schema), array('options', 'case-sensitive', 'allow-modifications', 'groups'));
        if(!empty($diff))
            throw new Exception('Неизвестные разделы описания конфигурации: ' . implode(', ', $diff));

        if(array_key_exists('case-sensitive', $schema))
        {
            $caseSensitive = $schema['case-sensitive'];
            $obj = new self(array(), $caseSensitive);
        }
        else
            $obj = new self(array());

        if(array_key_exists('options', $schema))
        {
            $options = $schema['options'];

            if(!is_array($options))
                throw new Exception("Раздел 'options' описания конфигурации должен быть массивом");

            foreach($options as $name => $option_schema)
            {
                if(!is_array($option_schema))
                    throw new Exception('Описание параметра настроек должно быть массивом');

                if(!array_key_exists('name', $option_schema) && is_string($name))
                    $option_schema['name'] = $name;

                $option = Option::create($option_schema);
                $obj->addOption($option);
            }
        }

        if(array_key_exists('allow-modifications', $schema))
        {
            $value = $schema['allow-modifications'];
            $obj->getOptions()->setAllowModifications($value);
        }

        if(array_key_exists('groups', $schema))
        {
            $groups = $schema['groups'];

            if(!is_array($groups))
                throw new Exception("Раздел 'groups' описания конфигурации должен быть массивом");

            $obj->setGroups($groups);
        }

        return $obj;
    }


    // --- Защищённые методы ---

    /**
     * Читает конфигурационный ini-файл.
     *
     * @param string $filename Имя обрабатываемого ini-файла.
     *
     * @return array Ассоциативный массив значений параметров, загруженных из указанного файла.
     *
     * @throws \Wheels\Config\Exception
     */
    protected function _processIniFile($filename)
    {
        if(!is_file($filename))
            throw new Exception("Файл '$filename' не найден");
        if(!is_readable($filename))
            throw new Exception("Файл '$filename' закрыт для чтения");

        $directory = realpath(dirname($filename));

        $err_handler = function ($errno, $message = '', $file = '', $line = 0) use ($filename) {
            throw new Exception("Ошибка чтения файла '$filename': [$errno] $message ($file:$line)");
        };
        set_error_handler($err_handler, E_WARNING);
        $data = parse_ini_file($filename, true);
        restore_error_handler();

        $processedData = array();
        foreach($data as $key => $value)
        {
            if(is_array($value))
            {
                if(strpos($key, self::KEY_SEP) !== false)
                {
                    $sections = explode(self::KEY_SEP, $key);
                    $processedData = array_merge_recursive($processedData, $this->_buildNestedSection($sections, $value, $directory));
                }
                elseif(strpos($key, self::SECTION_SEP) !== false)
                {
                    $sections = explode(self::SECTION_SEP, $key, 2);
                    $sections = array_map('trim', $sections);

                    $key = $sections[0];

                    $processedData[$key] = array();
                    for($i = count($sections) - 1; $i > 0; $i--)
                    {
                        $processedData[$key] = array_merge_recursive_distinct($processedData[$key], $processedData[$sections[$i]]);
                    }
                    $processedData[$key] = array_merge_recursive_distinct($processedData[$key], $this->_processSection($value, $directory));
                }
                else
                {
                    $processedData[$key] = $this->_processSection($value, $directory);
                }
            }
            else
            {
                $this->processKey($key, $value, $directory, $processedData);
            }
        }

        return $processedData;
    }

    /**
     * Process a nested section
     *
     * @param array  $sections
     * @param mixed  $value
     * @param string $directory Директория обрабатываемого ini-файла.
     *
     * @return array
     */
    protected function _buildNestedSection($sections, $value, $directory)
    {
        if(count($sections) == 0)
        {
            return $this->_processSection($value, $directory);
        }

        $nestedSection = array();

        $first = array_shift($sections);
        $nestedSection[$first] = $this->_buildNestedSection($sections, $value, $directory);

        return $nestedSection;
    }

    /**
     * Обрабатывает секцию.
     *
     * @param array  $section   Секция.
     * @param string $directory Директория обрабатываемого ini-файла.
     *
     * @return array
     */
    protected function _processSection(array $section, $directory)
    {
        $processedData = array();

        foreach ($section as $key => $value)
            $this->processKey($key, $value, $directory, $processedData);

        return $processedData;
    }

    /**
     * Обрабатывает ключ.
     *
     * @param string $key           Ключ.
     * @param string $value         Значение.
     * @param string $directory     Директория обрабатываемого ini-файла.
     * @param array  $processedData Обрабатываемый массив данных конфигурационного файла.
     *
     * @return array
     *
     * @throws \Wheels\Config\Exception
     */
    protected function processKey($key, $value, $directory, array &$processedData)
    {
        if(strpos($key, self::KEY_SEP) !== false)
        {
            list($first, $second) = explode(self::KEY_SEP, $key, 2);

            if (!strlen($first) || !strlen($second))
            {
                throw new Exception("Некорректный ключ '$key'");
            }
            elseif (!isset($processedData[$first]))
            {
                if ($first === '0' && !empty($processedData))
                {
                    $processedData = array($first => $processedData);
                }
                else
                {
                    $processedData[$first] = array();
                }
            }
            elseif (!is_array($processedData[$first]))
            {
                throw new Exception("Невозможно создать вложенный ключ для '$first', т. к. этот ключ уже есть");
            }

            $this->processKey($second, $value, $directory, $processedData[$first]);
        }
        elseif ($key === '@include')
        {
            if (is_null($directory))
                throw new Exception("Не удалось обработать выражение @include");

            $include = $this->_processIniFile($directory . '/' . $value);
            $processedData = array_replace_recursive($processedData, $include);
        }
        else
        {
            $processedData[$key] = $value;
        }
    }
}
