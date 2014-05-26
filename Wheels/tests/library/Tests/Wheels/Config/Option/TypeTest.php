<?php

namespace Tests\Wheels\Config\Option;

use Tests\TestCase;

use Wheels\Config\Option\Type;

class TypeTest extends TestCase
{
    public function testCreate()
    {
        $classname = 'Tests\Wheels\Config\Option\Type\Ttype';

        $actual = Type::create($classname);
        $this->assertInstanceOf($classname, $actual);

        $actual = Type::create($classname);
        $this->assertInstanceOf($classname, $actual);

        $actual = Type::create($classname . '[]');
        $this->assertInstanceOf('Wheels\Config\Option\Type\Tarray', $actual);
        $this->assertAttributeInstanceOf($classname, '_type', $actual);
    }

    public function testCreateExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Option\Type\Exception',
            'Тип (класс) unknown не найден'
        );

        Type::create('unknown');
    }

    public function testCreateExceptionB()
    {
        $classname = 'Tests\Wheels\Config\Option\Type\Twrong';
        
        $this->setExpectedException(
            '\Wheels\Config\Option\Type\Exception',
            "Класс {$classname} не является наследником Wheels\Config\Option\Type"
        );

        Type::create($classname);
    }
}
