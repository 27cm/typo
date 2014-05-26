<?php

namespace Tests\Wheels\Config\Option;

use Tests\TestCase;

use Wheels\Config\Option\Type;

class AbstractType extends TestCase
{
    /**
     * @var string
     */
    static protected $_typename;

    /**
     * @var \Wheels\Config\Option\Type
     */
    static protected $_type;

    /**
     * @var array
     */
    static protected $_dataConvert = array();
    
    /**
     * @var array
     */
    static protected $_dataValidate = array();

    static public function setUpBeforeClass()
    {
        static::$_typename = preg_replace('/^\\Wheels\\Config\\Option\\Type\\T/', '', static::getTestedClassname());
    }
    
    public function testCreate()
    {
        static::$_type = Type::create(static::$_typename);
        $this->assertInstanceOf(static::getTestedClassname(), static::$_type);
    }
    
    /**
     * @dataProvider dataConvert
     */
    public function testConvert($var, $expected)
    {
        $actual = static::$_type->convert($var);
        $this->assertEquals($expected, $actual);
    }

    public function dataConvert()
    {
        return static::$_dataConvert;
    }

    /**
     * @dataProvider dataValidate
     */
    public function testValidate($var, $expected)
    {
        $actual = static::$_type->validate($var);
        $this->assertEquals($expected, $actual);
    }

    public function dataValidate()
    {
        return static::$_dataValidate;
    }
}
