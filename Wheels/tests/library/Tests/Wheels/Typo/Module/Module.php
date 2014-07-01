<?php

namespace Tests\Wheels\Typo\Module;

use Tests\TestCase;
use Tests\JSONTestIterator;

use Wheels\Typo\Typo;

abstract class Module extends TestCase
{
    /**
     * @var \Wheels\Typo\Typo
     */
    static protected $typo;

    static public function setUpBeforeClass()
    {
        static::$typo = new Typo();

        $dir = static::getDir('data');
        static::$typo->setConfigDir($dir);
        static::$typo->addGroupsFromFile('config.ini');
    }

    /**
     * @dataProvider dataCases
     */
    public function testCases($input, $expected, $desc, $groups)
    {
        static::$typo->setDefaultOptions();

        if (!is_null($groups)) {
            static::$typo->setOptionsFromGroups($groups);
        }

        $actual = static::$typo->process($input);
        $this->assertEquals($expected, $actual, $desc);
    }

    public function dataCases()
    {
        $dir = static::getDir('data');
        return new JSONTestIterator($dir . DS . 'tests.json');
    }
}
