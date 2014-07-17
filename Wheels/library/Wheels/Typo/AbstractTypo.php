<?php

/**
 * Wheels Library
 *
 * @category Wheels
 * @package  Wheels\Typo
 */

namespace Wheels\Typo;

use Wheels\Config\Config;
use Wheels\Utility;

use Wheels\IAllowModifications;

/**
 *
 */
abstract class AbstractTypo implements ITypo, IAllowModifications
{
    /**
     * Конфигурация.
     *
     * @var \Wheels\Config\Config
     */
    protected $_config;


    // --- Открытые методы ---

    /**
     * Конструктор.
     *
     * @param array $options Массив настроек.
     */
    public function __construct(array $options = array())
    {
        $schema = static::_getConfigSchema();
        $this->_config = Config::create($schema, array(array(), false));
        $this->setOptions($options);
    }

    /**
     * Возвращает конфигурацию.
     *
     * @return \Wheels\Config\Config Конфигурация.
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Возвращает дирректорию с конфигурационными файлами.
     *
     * @return string
     */
    public function getConfigDir()
    {
        return $this->getConfig()->getDir();
    }

    /**
     * Возвращает значение параметра с заданным именем.
     *
     * @param string $name Название параметра.
     *
     * @return mixed Значение параметра с заданным именем.
     */
    public function getOption($name)
    {
        return $this->getConfig()->getOptionValue($name);
    }

    /**
     * Возвращает значения параметров.
     *
     * @return array Ассоциативный массив значений параметров.
     */
    public function getOptions()
    {
        return $this->getConfig()->getOptionsValues();
    }

    /**
     * {@inheritDoc}
     */
    public function setConfigDir($dir)
    {
//        $dir = $dir . DS . get_called_class();
//        $dir = Utility::realpath($dir);
//
//        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
//            throw new Exception("Не удалось создать директорию '{$dir}'");
//        }
//
//        $filename = 'config.ini';
//
//        $filepath = Utility::realpath($dir . DS . $filename);
//        if (!file_exists($filepath)) {
//            $file = fopen($filepath, 'w');
//            fclose($file);
//        }

        $this->getConfig()->setDir($dir);

    }

    public function addGroupsFromFile($filename)
    {
       $this->getConfig()->addGroupsFromFile($filename);
    }

    /**
     * Устанавливает значение параметра c заданным именем.
     *
     * @param string $name  Название параметра.
     * @param mixed  $value Значение параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOption($name, $value)
    {
        $this->getConfig()->setOptionValue($name, $value);
    }

    /**
     * Устанавливает значения параметров.
     *
     * @param array $options Ассоциативный массив значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptions(array $options)
    {
        $this->getConfig()->setOptionsValues($options);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions()
    {
        $this->getConfig()->setOptionsValuesDefault();
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowModifications($value)
    {
        $this->getConfig()->setAllowModifications($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroup($name)
    {
        $this->getConfig()->setOptionsValuesFromGroup($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names)
    {
        $this->getConfig()->setOptionsValuesFromGroups($names);
    }

    /**
     * Сохраняет настройки.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function saveOptions()
    {
        $this->getConfig()->saveOptionsValues();
    }

    /**
     * Восстанавливает настройки.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function restoreOptions()
    {
        $this->getConfig()->restoreOptionsValues();
    }


    // --- Защищенные методы ---

    /**
     * Возвращает описание конфигурации.
     *
     * @return array Описание конфигурации.
     *
     * @throws \Wheels\Typo\Exception
     */
    static protected function _getConfigSchema()
    {
        $filename = Utility::realpath(static::_getDir('data') . '/schema.php');
        if (!file_exists($filename)) {
            throw new Exception("Файл с описанием конфигурации '{$filename}' не найден");
        }

        $schema = include $filename;
        if (!is_array($schema)) {
            throw new Exception("Файл '{$filename}' должен возвращать массив");
        }

        if (!array_key_exists('case-sensitive', $schema)) {
            $schema['case-sensitive'] = false;
        }

        return $schema;
    }

    static protected function _getDir($key)
    {
        $dirs = array(
            'root'   => WHEELS_DIR . DS . get_called_class() . DS . '..',
            'data' => WHEELS_DIR . DS . get_called_class() . DS . '..' . DS . '_data',
        );

        return $dirs[$key];
    }
}
