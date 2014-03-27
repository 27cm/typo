<?php

namespace Wheels\Config\Schema\Option;

use Wheels\Config\Schema\Option\Type;

class TypeTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $classname = 'Wheels\Config\Schema\Option\Type\Ttype';

        $actual = Type::create('type');
        $this->assertInstanceOf($classname, $actual);

        $actual = Type::create($classname);
        $this->assertInstanceOf($classname, $actual);

        $actual = Type::create('type[]');
        $this->assertInstanceOf('Wheels\Config\Schema\Option\Type\Tarray', $actual);
        $this->assertAttributeInstanceOf($classname, '_type', $actual);
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
     * @expectedExceptionMessage Класс Wheels\Config не является наследником Wheels\Config\Schema\Option\Type
     */
    public function testCreateWrongClassException()
    {
        Type::create('Wheels\Config');
    }
}
