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
use Wheels\Typo\Base\Exception;

/**
 * Класс AbstractTypo.
 */
abstract class AbstractTypo implements TypoInterface
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
        $this->_config = Config::create($schema);

        $this->setOptions($options);
        $this->getConfig()->getOptions()->addEventListener('offsetSet', array($this, 'onConfigOptionsOffsetSet'));
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
    public function setConfigDir($dir, $require = false)
    {
        $subDir = get_called_class();
        $subDir = str_replace(__NAMESPACE__ . '\\', '', $subDir);
        $subDir = $dir . DS . strtolower($subDir);
        $subDir = Utility::realpath($subDir);

        if (!is_dir($dir . DS . $subDir)) {
            mkdir()
        }

        $filename = 'config.ini';

        echo ($filename . '<br>');

        if (!is_file($filename)) {
            // @todo: создать каталог и создать там ini-файл
        }

        $filename = Utility::realpath($filename);

        if (!is_file($filename) !is_readable($filename))


        try {
            $this->getConfig()->setDir($dir);
            $this->getConfig()->setGroupsFromFile($filename);
        } catch(\Wheels\Config\Exception $e) {
            // @todo: отлавливать
            if ($require) {
                throw $e;
            }
        }
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
    public function setOptionsFromGroup($name, $required = false)
    {
        $names = array($name);
        $this->setOptionsFromGroups($names, $required);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names, $required = false)
    {
        $this->getConfig()->setOptionsValuesFromGroups($names, $required);
    }

    /**
     * Обработчик события изменения значения параметра.
     *
     * @param string                $name   Название параметра.
     * @param \Wheels\Config\Option $option Параметр.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function onConfigOptionsOffsetSet($name, $option)
    {

    }


    // --- Защищенные методы ---

    /**
     * Возвращает описание конфигурации.
     *
     * @return array Описание конфигурации.
     *
     * @throws \Wheels\Typo\Base\Exception
     */
    static protected function _getConfigSchema()
    {
        $filename = Utility::realpath(static::_getDir('config') . '/schema.php');
        if (!file_exists($filename)) {
            throw new Exception("Файл с описанием конфигурации '{$filename}' не найден");
        }

        $schema = include $filename;
        if (!is_array($schema)) {
            throw new Exception("Файл '{$filename}' должен возвращать массив");
        }

        return $schema;
    }

    static protected function _getDir($key)
    {
        $dirs = array(
            'root'   => WHEELS_DIR . DS . get_called_class() . '/..',
            'config' => WHEELS_DIR . DS . get_called_class() . '/../_config',
        );

        return $dirs[$key];
    }
}
