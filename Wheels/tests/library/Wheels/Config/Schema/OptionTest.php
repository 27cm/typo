<?php

namespace Wheels\Config\Schema\Option;

use Wheels\Config\Schema\Option\Type;

use Wheels\Typo\Exception;

class TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    static protected $_typename;

    /**
     * @var \Wheels\Config\Schema\Option\Type
     */
    static protected $_type;

    /**
     * @var \Wheels\Config\Schema\Option\Type\Tarray
     */
    static protected $_array_type;

    static protected $_testValidateData = array();
    
    static protected $_testArrayValidateData = array();

    static public function setUpBeforeClass()
    {
        static::$_typename = preg_replace('/Test$/', '', get_called_class());
        static::$_typename = preg_replace('/^Wheels\Config\Schema\Option\Type\T/', '', static::$_typename);

        static::$_type = Type::create(static::$_typename);
        static::$_array_type = Type::create(static::$_typename . '[]');
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Wheels\Config\Schema\Option\Type\T' . static::$_typename, static::$_type);

        $this->assertInstanceOf('Wheels\Config\Schema\Option\Type\Tarray', static::$_array_type);
        $this->assertAttributeInstanceOf('Wheels\Config\Schema\Option\Type\T' . static::$_typename, '_type', static::$_array_type);
    }

    /**
     * @expectedException        Wheels\Typo\Exception
     * @expectedExceptionMessage Тип (класс) unknown не найден
     */
    public function testCreateUnknownException()
    {
        Type::create('unknown');
    }

    /**
     * @expectedException        Wheels\Typo\Exception
     * @expectedExceptionMessage Класс Wheels\Config не является наследником класса Wheels\Config\Schema\Option\Type
     */
    public function testCreateWrongClassException()
    {
        Type::create('Wheels\Config');
    }

    /**
     * @dataProvider testValidateDataProvider
     */
    public function testValidate($var, $expected)
    {
        $actual = $static::$_type->validate($var);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider testArrayValidateDataProvider
     */
    public function testArrayValidate($var, $expected)
    {
        $actual = $static::$_type->validate($var);
        $this->assertEquals($expected, $actual);
    }

    public function testValidateDataProvider()
    {
        return static::$_testValidateData;
    }

    public function testArrayValidateDataProvider()
    {
        return static::$_testArrayValidateData;
    }
}
