<?php
require_once 'XMLTestIterator.php';
require_once 'JSONTestIterator.php';
use Wheels\Typo;

/**
 * Абстрактный класс для теста модулей.
 *
 * Конкретные классы тестов находятся в директории Module, во всех них тестируются загруженные XML-файлы из папки resources через метод testXMLFiles
 */
abstract class AbstractModule extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Wheels\Typo
     */
    static protected $typo;

    static public $providerGetModule
        = array(
            array('\\Wheels\\Typo\\Module\\Html', '\\Wheels\\Typo\\Module\\Html'),
            array('\\Wheels\\Typo\\Module\\Html', '/Typo/Module/Html'),
            array('\\Wheels\\Typo\\Module\\Html', 'Module/Html'),
            array('\\Wheels\\Typo\\Module\\Html', 'module\\html'),
            array('\\Wheels\\Typo\\Module\\Html', ' html '),
        );

    static public function setUpBeforeClass()
    {
        self::$typo = new Typo();
        self::$typo->setConfigDir(TESTS_DIR . DS . 'config' . DS . get_called_class());
    }

    public function XMLProvider()
    {
        $filename = preg_replace('~Test$~', '', get_called_class()) . '.xml';
        return new XMLTestIterator('resources' . DS . $filename);
    }

    public function JSONProvider()
    {
        $filename = preg_replace('~Test$~', '', get_called_class()) . '.json';

        return new JSONTestIterator('resources' . DS . "json" . DS . $filename);
    }

    /**
     * @dataProvider self::$providerGetModule
     */
    public function testGetModule($expected, $name)
    {
        $actual = self::$typo->getModule($name);
        $this->assertInstanceOf($expected, $actual);
    }

    public function testGetModuleNull()
    {
        $actual = self::$typo->getModule('tml');
        $this->assertNull($actual);
    }

    /**
     * @dataProvider XMLProvider
     */
    public function testXMLFiles($input, $expected, $desc, $section)
    {
        static $old_section = null;
        if (!isset($old_section) || $section != $old_section) {
            self::$typo->setOptions($section);
            $old_section = $section;
        }
        $output = self::$typo->process($input);
        $this->assertEquals($expected, $output, $desc);
    }

    /**
     * @dataProvider JSONProvider
     */
    public function testJSONFiles($input, $expected, $desc, $section)
    {
        static $old_section = null;
        if (!isset($old_section) || $section != $old_section) {
            self::$typo->setOptions($section);
            $old_section = $section;
        }
        $output = self::$typo->process($input);
        $this->assertEquals($expected, $output, $desc);
    }
}