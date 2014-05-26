<?php

namespace Tests\Wheels\Typo\Module;

use Tests\JSONTestIterator;

use Wheels\Typo\Typo;

/**
 * Абстрактный класс для теста модулей.
 *
 * Конкретные классы тестов находятся в директории Module, во всех них тестируются загруженные XML-файлы из папки resources через метод testXMLFiles
 */
abstract class AbstractModule extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Wheels\Typo\Typo
     */
    static protected $typo;

    static public $providerGetModule = array(
        array('\\Wheels\\Typo\\Module\\Html', '\\Wheels\\Typo\\Module\\Html'),
        array('\\Wheels\\Typo\\Module\\Html', '/Typo/Module/Html'),
        array('\\Wheels\\Typo\\Module\\Html', 'Module/Html'),
        array('\\Wheels\\Typo\\Module\\Html', 'module\\html'),
        array('\\Wheels\\Typo\\Module\\Html', ' html '),
    );

    static public function setUpBeforeClass()
    {
        static::$typo = new Typo();
        static::$typo->setConfigDir(TESTS_DIR . DS . get_called_class() . DS . 'config');
    }

    public function JSONProvider()
    {
        return new JSONTestIterator(TESTS_DIR . DS . get_called_class() . DS . 'resources' . DS . 'tests.json');
    }

    /**
     * @dataProvider JSONProvider
     */
    public function testJSONFiles($input, $expected, $desc, $section)
    {
        static::$typo->setDefaultOptions();
        static::$typo->setOptionsFromGroups($section, true);

        $actual = static::$typo->process($input);
        $this->assertEquals($expected, $actual, $desc);
    }
}