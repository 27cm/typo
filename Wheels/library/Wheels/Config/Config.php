<?php

/**
 * Wheels Library
 *
 * @category Wheels
 * @package  Wheels\Config
 */

namespace Wheels\Config;

use Wheels\Config\Option\Collection as OptionsCollection;
use Wheels\Config\Option;
use Wheels\Utility;

/**
 * Класс для работы с параметрами настроек.
 *
 * Объект этого класса содержит описания параметров,
 * включающие названия, типы, значения параметров по умолчанию и т.п.
 *
 * Для удобства наборы значений параметров можно объединять в именованные группы.
 * Группы значений параметров могут пыть загружены из ini-файла.
 */
class Config
{
    /**
     * Параметры.
     *
     * @var \Wheels\Config\Option\Collection|\Wheels\Config\Option[]
     */
    protected $_options;

    /**
     * Группы значений параметров.
     *
     * @var array[]
     */
    protected $_groups = array();

    /**
     * Директория с конфигурационными файлами.
     *
     * @var string
     */
    protected $_dir;


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
        $this->_options = new OptionsCollection($options, $caseSensitive);
    }

    /**
     * Возвращает параметр с заданным именем.
     *
     * @param string $name Название параметра.
     *
     * @return \Wheels\Config\Option Параметр с заданным именем.
     *
     * @throws \Wheels\Config\Exception
     */
    public function getOption($name)
    {
        if (!$this->getOptions()->offsetExists($name)) {
            throw new Exception("Неизвестный параметр '{$name}'");
        }

        return $this->_options[$name];
    }

    /**
     * Возвращает параметры.
     *
     * @return \Wheels\Config\Option\Collection|\Wheels\Config\Option[] Коллекция параметров.
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Возвращает значение параметра с заданным именем.
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
     * Возвращает значения параметров.
     *
     * @return array Ассоциативный массив значений параметров.
     */
    public function getOptionsValues()
    {
        return $this->getOptions()->getValues();
    }

    /**
     * Возвращает группу значений параметров с заданным именем.
     *
     * @param string $name Название группы значений параметров.
     *
     * @return array Группа значений параметров с заданным именем.
     *
     * @throws \Wheels\Config\Exception
     */
    public function getGroup($name)
    {
        if (!array_key_exists($name, $this->_groups)) {
            throw new Exception("Группа значений параметров '$name' не найдена");
        }

        return $this->_groups[$name];
    }

    /**
     * Возвращает группы значений параметров.
     *
     * @return array Ассоциативный массив групп значений параметров.
     */
    public function getGroups()
    {
        return $this->_groups;
    }

    /**
     * Возвращает директорию с конфигурационными файлами.
     *
     * @return string Директория с конфигурационными файлами.
     */
    public function getDir()
    {
        return $this->_dir;
    }

    /**
     * Добавляет параметр.
     *
     * @param \Wheels\Config\Option $option Параметр.
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
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * Устанавливает параметры.
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
     * Устанавливает значение параметра c заданным именем.
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
        foreach ($options as $name => $value) {
            $this->setOptionValue($name, $value);
        }
    }

    /**
     * Устанавливает разрешение изменять параметры.
     *
     * @param bool $value true, если необходимо разрешить добавлять, изменять и удалять
     *                    параметры, и false - в противном случае.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setAllowModifications($value)
    {
        $this->getOptions()->setAllowModifications($value);
    }

    /**
     * Устанавливает значение параметра по умолчанию
     * в качестве текущего значения для параметра c заданным именем.
     *
     * @param string $name Название параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionValueDefault($name)
    {
        $this->getOption($name)->setValueDefault();
    }

    /**
     * Устанавливает значения параметров по умолчанию
     * в качестве текущих значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionsValuesDefault()
    {
        $this->getOptions()->setValueDefault();
    }

    /**
     * Добавляет группу значений параметров.
     *
     * @param int|float|string|bool $name  Название группы значений параметров.
     * @param array                 $group Ассоциативный массив значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Exception
     */
    public function addGroup($name, array $group)
    {
        if (!is_scalar($name)) {
            throw new Exception('Название группы настроек должно быть скалярным значением');
        }

        if (array_key_exists($name, $this->getGroups())) {
            throw new Exception("Группа настроек '$name' уже задана");
        }

        $this->_groups[$name] = $group;
    }

    /**
     * Добавляет группы значений параметров.
     *
     * @param array $groups Ассоциативный массив групп значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function addGroups(array $groups)
    {
        foreach ($groups as $name => $group) {
            $this->addGroup($name, $group);
        }
    }

    /**
     * Устанавливает группы значений параметров.
     *
     * @param array $groups Ассоциативный массив групп значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setGroups(array $groups)
    {
        $this->_groups = array();
        $this->addGroups($groups);
    }

    /**
     * Устанавливает директорию с конфигурационными файлами.
     *
     * @param string $dir Директория.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Exception
     */
    public function setDir($dir)
    {
        if (!is_dir($dir)) {
            throw new Exception("Каталог '{$dir}' не найден или не является каталогом");
        }
//        elseif (!is_writable($dir)) {
//            throw new Exception("Каталог '{$dir}' не доступен для записи");
//        } elseif (!is_readable($dir)) {
//            throw new Exception("Каталог '{$dir}' не доступен для чтения");
//        }

        $this->_dir = $dir;
    }

    /**
     * Добавляет группы значений параметров из ini-файла.
     *
     * @param string $filename Имя ini-файла.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function addGroupsFromFile($filename)
    {
        $groups = $this->_processIniFile($filename);

        foreach ($groups as $name => $group) {
            if (is_array($group)) {
                $this->addGroup($name, $group);
            }
        }
    }

    /**
     * Устанавливает группы значений параметров из ini-файла.
     *
     * @param string $filename Имя ini-файла.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setGroupsFromFile($filename)
    {
        $this->_groups = array();
        $this->addGroupsFromFile($filename);
    }

    /**
     * Устанавливает значения параметров из заданной группы значений параметров.
     *
     * @param int|float|string|bool $name Название группы значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionsValuesFromGroup($name)
    {
        $options = $this->getGroup($name);
        $this->setOptionsValues($options);
    }

    /**
     * Устанавливает значения параметров из заданных групп значений параметров.
     *
     * @param array $names Массив названий групп.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionsValuesFromGroups(array $names)
    {
        foreach ($names as $name) {
            $this->setOptionsValuesFromGroup($name);
        }
    }

    /**
     * Создаёт объект класса по его описанию.
     *
     * @param array $schema  Ассоциативный массив с описанием конфигурации.
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
        $sections = array('options', 'case-sensitive', 'allow-modifications', 'groups');

        $diff = array_diff(array_keys($schema), $sections);
        if (!empty($diff)) {
            throw new Exception('Неизвестные разделы описания конфигурации: ' . implode(', ', $diff));
        }

        if (array_key_exists('case-sensitive', $schema)) {
            $caseSensitive = $schema['case-sensitive'];
            $obj = new self(array(), $caseSensitive);
        } else {
            $obj = new self(array());
        }

        if (array_key_exists('options', $schema)) {
            $options = $schema['options'];

            if (!is_array($options)) {
                throw new Exception("Раздел 'options' описания конфигурации должен быть массивом");
            }

            foreach ($options as $name => $option_schema) {
                if (is_array($option_schema) && !array_key_exists('name', $option_schema)) {
                    $option_schema['name'] = $name;
                }

                $option = Option::create($option_schema);
                $obj->addOption($option);
            }
        }

        if (array_key_exists('allow-modifications', $schema)) {
            $value = $schema['allow-modifications'];
            $obj->getOptions()->setAllowModifications($value);
        }

        if (array_key_exists('groups', $schema)) {
            $groups = $schema['groups'];
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
        if (isset($this->_dir)) {
            $filename = $this->_dir . DS . $filename;
        }

        $filename = Utility::realpath($filename);

        if (!is_file($filename)) {
            throw new Exception("Файл '$filename' не найден");
        }
        if (!is_readable($filename)) {
            throw new Exception("Файл '$filename' закрыт для чтения");
        }

        $directory = realpath(dirname($filename));

        $err_handler = function ($errno, $message = '', $file = '', $line = 0) use ($filename) {
            throw new Exception("Ошибка чтения файла '$filename': [$errno] $message ($file:$line)");
        };
        set_error_handler($err_handler, E_WARNING);
        $data = parse_ini_file($filename, true);
        restore_error_handler();

        $processedData = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (strpos($key, self::KEY_SEP) !== false) {
                    $sections = explode(self::KEY_SEP, $key);
                    $processedData = array_merge_recursive(
                        $processedData, $this->_buildNestedSection($sections, $value, $directory)
                    );
                } elseif (strpos($key, self::SECTION_SEP) !== false) {
                    $sections = explode(self::SECTION_SEP, $key, 2);
                    $sections = array_map('trim', $sections);

                    $key = $sections[0];

                    $processedData[$key] = array();
                    for ($i = count($sections) - 1; $i > 0; $i--) {
                        $processedData[$key] = array_merge_recursive_distinct(
                            $processedData[$key], $processedData[$sections[$i]]
                        );
                    }
                    $processedData[$key] = array_merge_recursive_distinct(
                        $processedData[$key], $this->_processSection($value, $directory)
                    );
                } else {
                    $processedData[$key] = $this->_processSection($value, $directory);
                }
            } else {
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
        if (count($sections) == 0) {
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

        foreach ($section as $key => $value) {
            $this->processKey($key, $value, $directory, $processedData);
        }

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
        if (strpos($key, self::KEY_SEP) !== false) {
            list($first, $second) = explode(self::KEY_SEP, $key, 2);

            if (!strlen($first) || !strlen($second)) {
                throw new Exception("Некорректный ключ '$key'");
            } elseif (!isset($processedData[$first])) {
                if ($first === '0' && !empty($processedData)) {
                    $processedData = array($first => $processedData);
                } else {
                    $processedData[$first] = array();
                }
            } elseif (!is_array($processedData[$first])) {
                throw new Exception("Невозможно создать вложенный ключ для '$first', т. к. этот ключ уже есть");
            }

            $this->processKey($second, $value, $directory, $processedData[$first]);
        } elseif ($key === '@include') {
            if (is_null($directory)) {
                throw new Exception("Не удалось обработать выражение @include");
            }

            $include = $this->_processIniFile($directory . '/' . $value);
            $processedData = array_replace_recursive($processedData, $include);
        } else {
            $processedData[$key] = $value;
        }
    }
}
