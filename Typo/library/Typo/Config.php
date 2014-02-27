<?php

namespace Typo;

use Typo\Exception;

/**
 * 
 */
class Config
{
    /**
     * Разделитель ключей параметров.
     *
     * @var string
     */
    protected $sep = '.';

    /**
     * Директория обрабатываемого файла.
     *
     * @var string
     */
    protected $directory;

    /**
     * Читает конфигурационный ini-файл.
     *
     * @param string $filename  Путь к файлу.
     *
     * @return array
     *
     * @throws \Typo\Exception
     */
    public function fromFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename))
        {
            Module::throwException(Exception::E_RUNTIME, "Файл '$filename' не найден или закрыт для чтения");
        }

        $this->directory = dirname($filename);

        $err_handler = function ($error, $message = '', $file = '', $line = 0) use ($filename) {
            Module::throwException(Exception::E_RUNTIME, "Ошибка чтения INI файла '$filename': $message");
        };
        set_error_handler($err_handler, E_WARNING);
        $ini = parse_ini_file($filename, true);
        restore_error_handler();

        return $this->process($ini);
    }

    /**
     * Обрабатывает данные INI файла.
     *
     * @param array $data   Массив данных INI файла, возвращаемый функцией parse_ini_file()
     *
     * @return array
     */
    protected function process(array $data)
    {
        $config = array();

        foreach($data as $section => $value)
        {
            if(is_array($value))
            {
                if(strpos($section, $this->sep) !== false)
                {
                    $sections = explode($this->sep, $section);
                    $config = array_merge_recursive($config, $this->buildNestedSection($sections, $value));
                }
                else
                {
                    $config[$section] = $this->processSection($value);
                }
            }
            else
            {
                $this->processKey($section, $value, $config);
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
    private function buildNestedSection($sections, $value)
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

     * @throws \Typo\Exception
     */
    protected function processKey($key, $value, array &$config)
    {
        if(strpos($key, $this->sep) !== false)
        {
            list($first, $second) = explode($this->sep, $key, 2);

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
            if (is_null($this->directory))
                Module::throwException(Exception::E_RUNTIME, "Не удалось обработать выражение @include");

            $reader = clone $this;
            $include = $reader->fromFile($this->directory . '/' . $value);
            $config  = array_replace_recursive($config, $include);
        }
        else
        {
            $config[$key] = $value;
        }
    }
}
