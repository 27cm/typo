<?php

namespace Tests\Wheels\Config;

use Wheels\Config\Config;
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
        static::$configDir = realpath(TESTS_DIR . DS . 'config' . DS . str_replace(__NAMESPACE__, '', __CLASS__));

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

    public function testGetGroups()
    {
        $config = new Config();

        $expected = array();
        $actual = $config->getGroups();
        $this->assertEquals($expected, $actual);
    }

    public function testGetGroup()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $config = new Config($options);

        $name = 'group';
        $expected = array(
            $a->getName() => 'valueA',
            $b->getName() => 'valueB',
        );
        $config->addGroup($name, $expected);

        $actual = $config->getGroup($name);
        $this->assertEquals($expected, $actual);
    }

    public function testGetGroupException()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $groups = array(
            'groupA' => array(
                $a->getName() => 'valueA1',
            ),
            'groupB' => array(
                $a->getName() => 'valueA2',
                $b->getName() => 'valueB2',
            ),
        );

        $options = array($a, $b);
        $config = new Config($options);
        $config->setGroups($groups);

        $name = 'unknown';
        $this->setExpectedException(
            '\Wheels\Config\Exception',
            "Группа настроек '$name' не найдена"
        );

        $actual = $config->getGroup($name);
    }

    public function testSetOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $optionsData = array(
            array($a, $b),
            array($b, $c),
        );

        $config = new Config();

        foreach ($optionsData as $options) {
            $config->setOptions($options);
            $actual = $config->getOptions();
            $expected = new Collection($options);
            $this->assertEquals($expected, $actual);
        }
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

    public function testSetGroups()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $groupsData = array(
            array(
                'a' => array(
                    $a->getName() => 'valueA1',
                ),
                'b' => array(
                    $a->getName() => 'valueA2',
                    $b->getName() => 'valueB2',
                ),
            ),
            array(
                'b' => array(
                    $a->getName() => 'valueA2',
                ),
                'c' => array(
                    $a->getName() => 'valueA3',
                    $b->getName() => 'valueB3',
                ),
            )
        );

        $options = array($a, $b);
        $config = new Config($options);

        foreach ($groupsData as $groups) {
            $config->setGroups($groups);
            $actual = $config->getGroups();
            $expected = $groups;
            $this->assertEquals($expected, $actual);
        }
    }

    public function testAddGroups()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $groupsData = array(
            array(
                'a' => array(
                    $a->getName() => 'valueA1',
                ),
                'b' => array(
                    $a->getName() => 'valueA2',
                    $b->getName() => 'valueB2',
                ),
            ),
            array(
                'c' => array(
                    $a->getName() => 'valueA2',
                ),
                'd' => array(
                    $a->getName() => 'valueA3',
                    $b->getName() => 'valueB3',
                ),
            )
        );

        $options = array($a, $b);
        $config = new Config($options);

        $expected = array();
        foreach ($groupsData as $groups) {
            $config->addGroups($groups);
            $actual = $config->getGroups();
            $expected = array_merge($expected, $groups);
            $this->assertEquals($expected, $actual);
        }
    }

    public function testAddGroup()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $groups = array(
            'a' => array(
                $a->getName() => 'valueA1',
            ),
            'b' => array(
                $a->getName() => 'valueA2',
                $b->getName() => 'valueB2',
            ),
        );

        $options = array($a, $b);
        $config = new Config($options);

        $expected = array();
        foreach ($groups as $name => $group) {
            $config->addGroup($name, $group);
            $actual = $config->getGroups();
            $expected[$name] = $group;
            $this->assertEquals($expected, $actual);
        }
    }

    public function testSetOptionsValuesFromGroup()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 1, 'int');

        $groups = array(
            'groupA' => array(
                $a->getName() => 'valueA1',
                $c->getName() => 2,
            ),
            'groupB' => array(
                $a->getName() => 'valueA2',
            ),
        );

        $options = array($a, $b, $c);
        $config = new Config($options);
        $config->addGroups($groups);

        $config->setOptionsValuesFromGroup('groupA');
        $actual = $config->getOptionsValues();
        $expected = array(
            $a->getName() => 'valueA1',
            $b->getName() => 'defaultB',
            $c->getName() => 2,
        );
        $this->assertEquals($expected, $actual);
    }

    public function testSetOptionsValuesFromGroups()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 1, 'int');

        $groups = array(
            'groupA' => array(
                $a->getName() => 'valueA1',
                $c->getName() => 2,
            ),
            'groupB' => array(
                $a->getName() => 'valueA2',
            ),
        );
        $names = array_keys($groups);

        $options = array($a, $b, $c);
        $config = new Config($options);
        $config->setGroups($groups);

        $config->setOptionsValuesFromGroups($names);
        $actual = $config->getOptionsValues();
        $expected = array(
            $a->getName() => 'valueA2',
            $b->getName() => 'defaultB',
            $c->getName() => 2,
        );
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

    public function testAddGroupsFromFile()
    {
        $filesData = array(
            array(
                'filename' => static::$configDir . DS . 'configA.ini',
                'groups' => array(
                    'groupA1' => array(
                        'simple' => 'simpleA',
                        'string' => 'stringA',
                        'param' => array(
                            'string' => 'stringA',
                            'bool' => array(
                                'enabled' => true,
                                'disabled' => false,
                            ),
                        ),
                        'array' => array('A1', 'A2', 'A3'),
                    ),
                    'groupA2' => array(
                        'simple' => 'simpleA',
                        'string' => 'stringB',
                        'param' => array(
                            'string' => 'stringB',
                            'bool' => array(
                                'enabled'  => false,
                                'disabled' => false,
                            ),
                        ),
                        'array' => array('A1', 'A2', 'A3', 'B1', 'B2'),
                    ),
                ),
            ),
            array(
                'filename' => static::$configDir . DS . 'configB.ini',
                'groups' => array(
                    'B' => array(
                        'simple' => 'simpleC',
                        'param' => array(
                            'string' => 'stringC',
                        ),
                    ),
                ),
            ),
        );

        $expected = array();
        foreach ($filesData as $f) {
            $filename = $f['filename'];
            $groups = $f['groups'];

            chmod($filename, 0777);
            static::$config->addGroupsFromFile($filename);

            $actual = static::$config->getGroups();
            $expected = array_merge($expected, $groups);
            $this->assertEquals($expected, $actual);
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
