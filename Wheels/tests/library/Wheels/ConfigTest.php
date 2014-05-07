<?php

namespace Wheels;

use Wheels\Config;
use Wheels\Config\Option;
use Wheels\Config\Option\Collection;

use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Wheels\Config
     */
    static protected $config;

    /**
     * Директория с тестовыми INI файлами.
     *
     * @var string
     */
    static protected $configDir;

    static public function setUpBeforeClass()
    {
        static::$configDir = realpath(TEST_DIR . DS . 'config' . DS . str_replace(__NAMESPACE__, '', __CLASS__));

        $config = array(
            'options' => array(
                'simple' => array(
                    'default' => 'default',
                    'type'    => 'string',
                ),
                'string' => array(
                    'default' => 'default',
                    'type'    => 'string',
                ),
                'param'  => array(
                    'default' => array(),
                    'type'    => 'mixed[]',
                ),
                'array'  => array(
                    'default' => array(),
                    'type'    => 'string[]',
                ),
            ),
        );
        static::$config = Config::create($config);
    }

    public function testGetOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $config = new Config($options);

        $expected = new Collection($options);
        $actual = $config->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testGetOption()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $config = new Config($options);

        $expected = $b;
        $actual = $config->getOption('nameB');
        $this->assertEquals($expected, $actual);
    }

    public function testGetOptionsValues()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $config = new Config($options);

        $expected = array(
            $a->getName() => $a->getValue(),
            $b->getName() => $b->getValue(),
        );
        $actual = $config->getOptionsValues();
        $this->assertEquals($expected, $actual);
    }

    public function testGetOptionValue()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $config = new Config($options);

        $expected = 'defaultA';
        $actual = $config->getOptionValue('nameA');
        $this->assertEquals($expected, $actual);
    }

    public function testSetOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $config = new Config();
        $options = array($a, $b);
        $config->setOptions($options);

        $expected = new Collection($options);
        $actual = $config->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testAddOption()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $options = array($a);
        $config = new Config($options);

        $config->addOption($b);
        $config->addOption($c);

        $options = array($a, $b, $c);
        $expected = new Collection($options);
        $actual = $config->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testAddOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $options = array($a);
        $config = new Config($options);

        $options = array($b, $c);
        $config->addOptions($options);

        $options = array($a, $b, $c);
        $expected = new Collection($options);
        $actual = $config->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testSetOptionValue()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $options = array($a, $b);
        $config = new Config($options);

        $expected = 'valueB';
        $config->setOptionValue('nameB', $expected);

        $actual = $config->getOptionValue('nameB');
        $this->assertEquals($expected, $actual);
    }

    public function testCreate()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $modifications = true;
        $case = false;
        $options = array(
            $a->getName() => array(
                'default' => $a->getDefault(),
            ),
            $b->getName() => array(
                'default' => $b->getDefault(),
                'type'    => 'string',
            ),
            $c->getName() => array(
                'default' => $c->getDefault(),
            ),
        );

        $config = array(
            'allow-modifications' => $modifications,
            'case-sensitive'      => $case,
            'options'             => $options,
        );
        $actual = Config::create($config);

        $options = array($a, $b, $c);
        $expected = new Config($options, $case);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Exception',
            'Неизвестные разделы описания конфигурации: unknown'
        );

        $config = array(
            'unknown' => 1,
        );
        Config::create($config);
    }

    public function testCreateExceptionB()
    {
        $this->setExpectedException(
            '\Wheels\Config\Exception',
            "Раздел 'options' описания конфигурации должен быть массивом"
        );

        $config = array(
            'options' => 1,
        );
        Config::create($config);
    }

    public function testCreateExceptionC()
    {
        $this->setExpectedException(
            '\Wheels\Config\Exception',
            'Описание параметра настроек должно быть массивом'
        );

        $config = array(
            'options' => array(1),
        );
        Config::create($config);
    }

    public function testSetOptionsValuesFromFile()
    {
        $sections = array(
            'default' => array(
                'simple' => 'simpleA',
                'string' => 'stringA',
                'param'  => array(
                    'string' => 'stringA',
                    'bool'   => array(
                        'enabled'  => true,
                        'disabled' => false,
                    ),
                ),
                'array'  => array('A1', 'A2', 'A3'),
            ),
            'A'       => array(
                'simple' => 'simpleA',
                'string' => 'stringB',
                'param'  => array(
                    'string' => 'stringB',
                    'bool'   => array(
                        'enabled'  => false,
                        'disabled' => false,
                    ),
                ),
                'array'  => array('A1', 'A2', 'A3', 'B1', 'B2'),
            ),
            'B'       => array(
                'simple' => 'simpleC',
                'string' => 'default',
                'param'  => array(
                    'string' => 'stringC',
                ),
                'array'  => array(),
            ),
        );

        $filename = static::$configDir . DS . 'config.ini';
        chmod($filename, 0777);

        foreach (array_keys($sections) as $section) {
            foreach (static::$config->getOptions() as $option) {
                $option->setValueDefault();
            }

            if ($section === 'default') {
                static::$config->setOptionsValuesFromFile($filename);
            } else {
                static::$config->setOptionsValuesFromFile($filename, $section);
            }

            $actual = static::$config->getOptionsValues();
            $expected = $sections[$section];
            $this->assertEquals($expected, $actual, $section);
        }
    }

    public function testSetOptionsValuesFromFileExceptionA()
    {
        $filename = static::$configDir . DS . 'unknown.ini';

        $this->setExpectedException(
            '\Wheels\Config\Exception',
            "Файл '$filename' не найден"
        );

        static::$config->setOptionsValuesFromFile($filename);
    }

    public function testSetOptionsValuesFromFileExceptionB()
    {
        $filename = static::$configDir . DS . 'config.ini';

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $this->setExpectedException(
                '\Wheels\Config\Exception',
                "Файл '$filename' закрыт для чтения"
            );
        }

        // Закрываем файл для чтения
        $mode = fileperms($filename);
        chmod($filename, 0333);

        static::$config->setOptionsValuesFromFile($filename);

        // Восстанавливаем права доступа к файлу
        chmod($filename, $mode);
    }
}
