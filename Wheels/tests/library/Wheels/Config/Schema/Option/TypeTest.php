<?php

namespace Wheels\Config\Schema\Option;

use Wheels\Config\Schema\Option\Type;

use PHPUnit_Framework_TestCase;

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

    public function testCreateExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Type\Exception',
            'Тип (класс) unknown не найден'
        );
        
        Type::create('unknown');
    }

    public function testCreateExceptionB()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Type\Exception',
            'Класс Wheels\Config\Schema\Option\Type\Twrong не является наследником Wheels\Config\Schema\Option\Type'
        );
        
        Type::create('wrong');
    }
}
