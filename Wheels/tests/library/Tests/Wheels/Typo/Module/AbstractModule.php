<?php

namespace Tests\Wheels\Typo\Module;

use Tests\TestCase;
use Tests\JSONTestIterator;

use Wheels\Typo\Typo;

/**
 * Абстрактный класс для теста модулей.
 *
 * Конкретные классы тестов находятся в директории Module, во всех них тестируются загруженные XML-файлы из папки resources через метод testXMLFiles
 */
abstract class AbstractModule extends TestCase
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
        
        $dir = static::getDir('config');
        static::$typo->setConfigDir($dir);
    }

    /**
     * @dataProvider dataCases
     */
    public function testCases($input, $expected, $desc, array $groups = null)
    {
        static::$typo->setDefaultOptions();

        if (!is_null($groups)) {
            static::$typo->setOptionsFromGroups($groups, true);
        }

        $actual = static::$typo->process($input);
        $this->assertEquals($expected, $actual, $desc);
    }
    
    public function dataCases()
    {
        $dir = static::getDir('resources');
        return new JSONTestIterator($dir . DS . 'tests.json');
    }
}