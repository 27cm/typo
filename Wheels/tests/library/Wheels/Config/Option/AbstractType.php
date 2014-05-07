<?php

namespace Wheels\Config\Option;

use Wheels\Config\Option\Type;

use PHPUnit_Framework_TestCase;

class AbstractType extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    static protected $_classname;

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
    static protected $_testValidateData = array();

    static public function setUpBeforeClass()
    {
        static::$_classname = preg_replace('/Test$/', '', get_called_class());
        static::$_typename = preg_replace('/^Wheels\Config\Option\Type\T/', '', static::$_classname);
    }

    public function testCreate()
    {
        static::$_type = Type::create(static::$_typename);
        $this->assertInstanceOf(static::$_classname, static::$_type);
    }

    /**
     * @dataProvider testValidateDataProvider
     */
    public function testValidate($var, $expected)
    {
        $actual = static::$_type->validate($var);
        if ($expected) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    public function testValidateDataProvider()
    {
        return static::$_testValidateData;
    }
}
