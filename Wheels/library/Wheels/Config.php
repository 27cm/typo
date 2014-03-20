<?php

namespace Wheels;

use Wheels\Config\Schema;

use Wheels\Typo\Module;
use Wheels\Typo\Exception;

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
     * Описание конфигурации.
     *
     * @var \Wheels\Config\Schema|NULL
     */
    protected $_schema = NULL;

    /**
     * Значения параметров.
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Загруженные секции.
     *
     * @var array
     */
    protected $_sections = array();

    /**
     * Директория обрабатываемого файла.
     *
     * @var string
     */
    private $_directory;


    // --- Константы ---

    /**
     * Разделитель ключей параметров.
     */
    const INI_KEY_SEP = '.';

    /**
     * Разделитель секций.
     */
    const INI_SECTION_SEP = ':';


    // --- Конструктор ---

    public function __construct(array $schema = NULL)
    {
        if(!is_null($schema))
        {
            $this->_schema = new Schema($schema);


        }

        $this->setDefaultOptions();
    }


    // --- Открытые методы ---

    public function getDefaultOptions()
    {
        $options = array();

        if($this->_isSchema())
        {
            foreach($this->_schema['options'] as $name => $option_schema)
            {
                $options[$name] = $option_schema['default'];
            }
        }

        return $options;
    }

    public function setOptions(array $options)
    {
        $exception = null;

        foreach($options as $name => $value)
        {
            try
            {
                $this->setOption($name, $value);
            }
            catch(Exception $e)
            {
                if(isset($exception))
                    $exception = new Exception($e->getMessage(), $e->getCode(), $exception);
                else
                    $exception = new Exception($e->getMessage(), $e->getCode());
            }
        }

        if(isset($exception))
            throw $exception;
    }

    public function setDefaultOptions()
    {
        $this->_options = array();
        $this->setOptions($this->getDefaultOptions());
    }

    public function setOptionsFromFile($filename, $section = NULL)
    {
        $options = $this->_processIniFile($filename, $section);

        $this->setOptions($options);
    }

    public function setOption($name, $value)
    {
        $name = $this->_prepareOptionName($name);

        $this->_validateOptionName($name);
        $this->_validateOptionValue($name, $value);

        $this->_options[$name] = $value;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($name)
    {
        $name = $this->_prepareOptionName($name);

        if(!array_key_exists($name, $this->_options))
            return Module::throwException(Exception::E_OPTION_NAME, "Неизвестный параметр '$name'");

        return $this->_options[$name];
    }

    protected function _isSchema()
    {
        return (!is_null($this->_schema));
    }

    protected function _isCaseSensitive()
    {
        return ($this->_isSchema() ? $this->_schema['case-sensitive'] : TRUE);
    }

    protected function _prepareSectionName($section)
    {
        return ($this->_isCaseSensitive() ? $section : strtolower($section));
    }

    protected function _prepareOptionName($name)
    {
        return ($this->_isCaseSensitive() ? $name : strtolower($name));
    }

    protected function _validateOptionName($name)
    {
        $name = $this->_prepareOptionName($name);

        if($this->_isSchema())
        {
            if(!array_key_exists($name, $this->_schema['options']))
                return Module::throwException(Exception::E_OPTION_NAME, "Неизвестный параметр '$name'");
        }
    }

    protected function _validateOptionValue($name, $value)
    {
        $name = $this->_prepareOptionName($name);

        if($this->_isSchema())
        {

        }
    }

    public function validateKeyValue($key, $value)
    {
        // @todo: Конфигурационный файл может отсутствовать
        // @todo: Перенести в класс Wheel, Сделать Config\Exception
        if(!array_key_exists($key, $this->_schema['options']))
            Module::throwException(Exception::E_RUNTIME, "Неизвестный параметр '$key'");

        $schema = $this->_schema['options'][$key];

        $type = $schema['type'];
        if(in_array($type, array('dir', 'int', 'integer', 'bool', 'float', 'numeric', 'real'), TRUE))
        {
            $func = 'is_' . $type;
            if(!$func($value))
            {
                $types_expected = array(
                    'dir'     => '',
                    'int'     => 'целым числом',
                    'integer' => '',
                    'bool'    => '',
                    'float'   => '',
                    'numeric' => '',
                    'real'    => '',
                    'string'  => 'строкой',
                );
                $types_actual = array();
                Module::throwException(Exception::E_RUNTIME, "Значение параметра '$key' должно быть {$types_expected[$type]}, a не " . $types_actual[gettype($value)]);
            }
        }
        if(in_array($type, array('dir[]', 'int[]', 'integer[]', 'bool[]', 'float[]', 'numeric[]', 'real[]'), TRUE))
        {
            $func = 'is_' . $type;
            if(!is_array($value))
            {
                $types_expected = array(
                    'dir'     => '',
                    'int'     => 'целых чисел',
                    'integer' => '',
                    'bool'    => '',
                    'float'   => '',
                    'numeric' => '',
                    'real'    => '',
                    'string'  => 'строк',
                );
                Module::throwException(Exception::E_RUNTIME, "Значение параметра '$key' должно быть массивом {$types_expected[$type]}, a не " . $types_expected[gettype($value)]);
            }
        }
    }

    public function sectionExists($section)
    {
        $section = $this->_prepareSectionName($section);

        return array_key_exists($section, $this->_sections);
    }

    public function getSection($section)
    {
        $section = $this->_prepareSectionName($section);

        if($this->sectionExists($section))
            return $this->_sections[$section];
        else
            Module::throwException(Exception::E_RUNTIME, "Раздел настроек '$section' не найден в конфигурационном файле '{$this->filename}'");
    }

    /**
     * Читает конфигурационный INI файл.
     *
     * @return array
     *
     * @throws \Wheels\Typo\Exception
     */
    protected function _processIniFile($filename, $section = NULL)
    {
        if(!is_file($filename))
            Module::throwException(Exception::E_RUNTIME, "Файл '$filename' не найден");
        if(!is_readable($filename))
            Module::throwException(Exception::E_RUNTIME, "Файл '$filename' закрыт для чтения");

        $this->_directory = realpath(dirname($filename));

        $err_handler = function ($error, $message = '', $file = '', $line = 0) use ($filename) {
            Module::throwException(Exception::E_RUNTIME, "Ошибка чтения файла '{$filename}': $message");
        };
        set_error_handler($err_handler, E_WARNING);
        $data = parse_ini_file($filename, true);
        restore_error_handler();

        return $this->_processData($data, $section);
    }

    /**
     * Обрабатывает данные INI файла.
     *
     * @param array $data   Массив данных INI файла, возвращаемый функцией parse_ini_file()
     *
     * @return array
     */
    protected function _processData(array $data, $section = NULL)
    {
        // @todo: если задана конкретная секция, то возвращаем только её
        // @todo: все секции по умолчанию наследую параметры вне секции

        $config = array();

        foreach($data as $key => $value)
        {
            if(is_array($value))
            {
                if(strpos($key, self::INI_KEY_SEP) !== false)
                {
                    $sections = explode(self::INI_KEY_SEP, $key);
                    $config = array_merge_recursive($config, $this->buildNestedSection($sections, $value));
                }
                elseif(strpos($key, self::INI_SECTION_SEP) !== false)
                {
                    $sections = explode(self::INI_SECTION_SEP, $key, 2);
                    $sections = array_map('trim', $sections);

                    $key = $sections[0];

                    $config[$key] = array();
                    for($i = count($sections) - 1; $i > 0; $i--)
                    {
                        $config[$key] = array_merge_recursive_distinct($config[$key], $config[$sections[$i]]);
                    }
                    $config[$key] = array_merge_recursive_distinct($config[$key], $this->processSection($value));
                }
                else
                {
                    $config[$key] = $this->processSection($value);
                }
            }
            else
            {
                $this->processKey($key, $value, $config);
            }
        }

        return $config;
    }

    /**
     * Process a nested section
     *
     * @param array $sections
     * @param mixed $value
     * @return array
     */
    protected function buildNestedSection($sections, $value)
    {
        if(count($sections) == 0)
        {
            return $this->processSection($value);
        }

        $nestedSection = array();

        $first = array_shift($sections);
        $nestedSection[$first] = $this->buildNestedSection($sections, $value);

        return $nestedSection;
    }

    /**
     * Обрабатывает секцию.
     *
     * @param array $section    Секция.
     *
     * @return array
     */
    protected function processSection(array $section)
    {
        $config = array();

        foreach ($section as $key => $value)
            $this->processKey($key, $value, $config);

        return $config;
    }

    /**
     * Обрабатывает ключ.
     *
     * @param string $key   Ключ.
     * @param string $value Значение.
     * @param array $config Обрабатываемый массив данных конфигурационного файла.

     * @return array

     * @throws \Wheels\Typo\Exception
     */
    protected function processKey($key, $value, array &$config)
    {
        if(strpos($key, self::INI_KEY_SEP) !== false)
        {
            list($first, $second) = explode(self::INI_KEY_SEP, $key, 2);

            if (!strlen($first) || !strlen($second))
            {
                Module::throwException(Exception::E_RUNTIME, "Некорректный ключ '$key'");
            }
            elseif (!isset($config[$first]))
            {
                if ($first === '0' && !empty($config))
                {
                    $config = array($first => $config);
                }
                else
                {
                    $config[$first] = array();
                }
            }
            elseif (!is_array($config[$first]))
            {
                Module::throwException(Exception::E_RUNTIME, "Невозможно создать вложенный ключ для '$first', т. к. этот ключ уже есть");
            }

            $this->processKey($second, $value, $config[$first]);
        }
        elseif ($key === '@include')
        {
            if (is_null($this->_directory))
                Module::throwException(Exception::E_RUNTIME, "Не удалось обработать выражение @include");

            $reader = clone $this;
            $include = $reader->_processIniFile($this->_directory . '/' . $value);
            $config  = array_replace_recursive($config, $include);
        }
        else
        {
            // @todo: Потенциальный баг
            if(in_array($value, array(1, '1'), true))
                $value = true;
            elseif(in_array($value, array(0, '0', ''), true))
                $value = false;

            $config[$key] = $value;
        }
    }

    static public function create(/* ... */)
    {
        if(func_num_args() == 2)
        {
            list($arg1, $arg2) = func_get_args();

            if(is_array($arg1))
                return static::createFromArray($arg1);
            elseif(is_string($arg1))
                return static::createFromFile($arg1);
        }
        elseif(func_num_args() == 3)
        {
            list($arg1, $arg2, $arg3) = func_get_args();

            if(is_string($arg1) && is_string($arg2))
                return static::createFromFile($arg1, $arg2);
        }

        return Module::throwException(Exception::E_UNKNOWN, 'Недопустимые параметры метода ' . __CLASS__ . '::' . __METHOD__ . ' ' . var_dump(func_get_args()));
    }

    static protected function createFromArray(array $options, array $schema = NULL)
    {
        $config = new Config($schema);
        $config->setOptions($options);
        return $config;
    }

    static protected function createFromFile($filename, $section = NULL, array $schema = NULL)
    {
        $config = new Config($schema);
        $config->setOptionsFromFile($filename, $section);
        return $config;
    }
}
