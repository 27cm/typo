<?php

namespace Tests\Wheels\Typo;

use Wheels\Typo\Typo;
use Wheels\Typo\Exception;
use Wheels\Typo\Module\Collection as ModulesCollection;
use Wheels\Typo\Module\Module;
use Wheels\Typo\Module\Core\Core;

//use Wheels\Typo\Module\Filepath;


class TypoTest extends PHPUnit_Framework_TestCase
{
    static protected $_defaultOptions = array(
        'charset' => 'UTF-8',
        'html-in-enabled' => true,
        'html-out-enabled' => false,
        'modules' => array('core', 'html', 'nobr', 'punct', 'space', 'symbol', 'url'),
        'module.core' => array(
            'e-convert' => false,
            'encoding' => Core::MODE_NONE,
        ),
        'module.html' => array(
            'safe-blocks' => array('<!-- -->', 'code', 'comment', 'pre', 'script', 'style'),
            'typo-attrs' => array('title', 'alt'),
            'paragraphs' => true,
        ),
        'module.nobr' => array(
            'open' => '<span style="word-spacing:nowrap;">',
            'close' => '</span>',
        ),
        'module.punct' => array(
            'spaces' => true,
            'auto' => false,
        ),
        'module.space' => array(
            'nessesary' => true,
            'nbsp' => true,
            'thinsp' => true,
            'remove' => true,
        ),
        'module.symbol' => array(),
        'module.url' => array(
            'attrs' => array(
                'target' => array(
                    'name'  => 'target',
                    'value' => '_blank',
                    'cond'  => '\Wheels\Typo\Module\Url::condTarget',
                ),
            ),
            'idn-convert' => true,
        ),
    );

    public function testGetModules()
    {
        $typo = new Typo();

        $names = array('core', 'html', 'nobr', 'punct', 'space', 'symbol', 'url');
        $modules = array();
        foreach ($names as $name) {
            $classname = Module::getModuleClassname($name);
            $modules[] = new $classname($typo);
        }

        $actual = $typo->getModules();
        $expected = new ModulesCollection($modules);
        $this->assertEquals($expected, $actual);
    }

    public function testGetModule()
    {
        $typo = new Typo();

        $name = 'core';
        $classname = Module::getModuleClassname($name);

        $actual = $typo->getModule($name);
        $expected = new $classname($typo);
        $this->assertEquals($expected, $actual);
    }

    public function testGetModuleException()
    {
        $name = 'unknown';

        $this->setExpectedException(
            '\Wheels\Typo\Exception',
            "Неизвестный модуль '{$name}'"
        );

        $typo = new Typo();
        $typo->getModule($name);
    }

    /**
     * @dataProvider testHasModuleDataProvider
     */
    public function testHasModule($name, $expected)
    {
        $typo = new Typo();

        $actual = $typo->hasModule($name);
        $this->assertEquals($expected, $actual);
    }

    public function testHasModuleDataProvider()
    {
        return array(
            array('core', true),
            array('html', true),
            array('nobr', true),
            array('punct', true),
            array('space', true),
            array('symbol', true),
            array('url', true),
            array('unknown', false),
        );
    }

    public function testGetOptions()
    {
        $typo = new Typo();

        $actual = $typo->getOptions();
        $expected = static::$_defaultOptions;
        $this->assertEquals($expected, $actual);
    }

    public function testSetOptions()
    {
        $typo = new Typo();

        $options = array(
            'charset' => 'WINDOWS-1251',
            'module.core' => array(
                'e-convert' => false,
                'encoding' => Core::MODE_HEX_CODES,
            ),
            'module.core.e-convert' => true,
            'module.nobr' => array(),
            'modules' => array('core', 'nobr'),
        );
        $typo->setOptions($options);

        $expected = array(

        );

        $actual = $typo->getOptions();
        $expected = $options;
        $this->assertEquals($expected, $actual);
    }

    public function testSetOption()
    {
        $typo = new Typo();

        $options = array(
            'charset' => 'WINDOWS-1251',
            'module.core.e-convert' => true,
            'core.encoding' => Core::MODE_HEX_CODES,
            'module.html' => array(),
            'modules' => array('core', 'html'),
        );

        $return = array(
            'charset' => 'WINDOWS-1251',
            'module.core.e-convert' => true,
            'core.encoding' => Core::MODE_HEX_CODES,
            'module.html' => array(),
            'modules' => array('core', 'html'),
        );

        foreach ($options as $name => $option) {
            $typo->setOption($name, $option);
            $actual = $typo->getOption($name);
            $expected = $return[$name];
            $this->assertEquals($expected, $actual);
        }
    }

    public function testSaveOptions()
    {
        $typo = new Typo();

        $options = array(
            'charset' => 'WINDOWS-1251',
            'module.core' => array(
                'e-convert' => false,
                'encoding' => Core::MODE_HEX_CODES,
            ),
        );
        $typo->setOptions($options);
        $savedOptions = $typo->getOptions();
        $typo->saveOptions();

        $options = array(
            'charset' => 'UTF-8',
            'module.core' => array(
                'e-convert' => true,
                'encoding' => Core::MODE_NONE,
            ),
        );
        $typo->setOptions($options);

        $typo->restoreOptions();
        $actual = $typo->getOptions();
        $expected = $savedOptions;
        $this->assertEquals($expected, $actual);
    }
}
