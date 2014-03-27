<?php

namespace Wheels\Config\Schema;

use Wheels\Config\Schema\Option;
use Wheels\Config\Schema\Option\Type\Tmixed;
use Wheels\Config\Schema\Option\Type\Tstring;

class OptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $expected = 'optionName';
        $option = new Option($expected, 'default');

        $actual = $option->getName();
        $this->assertEquals($expected, $actual);
    }

    public function testGetType()
    {
        $option = new Option('name', 'default');

        $actual = $option->getType();
        $expected = 'Wheels\Config\Schema\Option\Type\Tmixed';
        $this->assertInstanceOf($expected, $actual);

        $type = new Tstring();
        $option = new Option('name', 'default', $type);

        $actual = $option->getType();
        $expected = get_class($type);
        $this->assertInstanceOf($expected, $actual);
    }

    public function testGetDefault()
    {
        $expected = 'defaultValue';
        $option = new Option('name', $expected);

        $actual = $option->getDefault();
        $this->assertEquals($expected, $actual);
    }

    public function testGetDesc()
    {
        $option = new Option('name', 'default');

        $actual = $option->getDesc();
        $this->assertNull($actual);
    }

    public function testGetAliases()
    {
        $option = new Option('name', 'default');

        $actual = $option->getAliases();
        $expected = array();
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllowed()
    {
        $option = new Option('name', 'default');

        $actual = $option->getAllowed();
        $expected = array();
        $this->assertEquals($expected, $actual);
    }

    public function testSetName()
    {
        $option = new Option('nameA', 'default');
        $expected = 'nameB';
        $option->setName($expected);

        $actual = $option->getName();
        $this->assertEquals($expected, $actual);
    }

    public function testSetNameException()
    {
        $this->setExpectedException('\Wheels\Config\Schema\Option\Exception', 'Имя параметра должно быть строкой');

        $option = new Option('name', 'default');
        $option->setName(1);
    }

    public function testSetType()
    {
        $option = new Option('name', 'default');
        $type = new Tstring();
        $option->setType($type);

        $actual = $option->getType();
        $expected = get_class($type);
        $this->assertInstanceOf($expected, $actual);

        $type = new Tstring();
        $option = new Option('name', 'default', $type);
        $type = new Tmixed();
        $option->setType($type);

        $actual = $option->getType();
        $expected = get_class($type);
        $this->assertInstanceOf($expected, $actual);
    }

    public function testSetDefault()
    {
        $option = new Option('name', 'defaultA');
        $expected = 'defaultB';
        $option->setDefault($expected);


        $actual = $option->getDefault();
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultExceptionA()
    {
        $this->setExpectedException('\Wheels\Config\Schema\Option\Exception', "Недопустимое значение по умолчанию параметра 'name'");

        $type = new Tstring();
        $option = new Option('name', 'default', $type);
        $option->setDefault(1);
    }

    public function testSetDesc()
    {
        $option = new Option('name', 'default');
        $expected = 'description';
        $option->setDesc($expected);

        $actual = $option->getDesc();
        $this->assertEquals($expected, $actual);
    }

    public function testSetDescException()
    {
        $this->setExpectedException('\Wheels\Config\Schema\Option\Exception', "Текстовое описание параметра 'name' должно быть строкой");

        $option = new Option('name', 'default');
        $option->setDesc(1);
    }

    public function testSetAliases()
    {
        $option = new Option('name', 'default');
        $expected = array(
            'aliasA' => 'valueA',
            'aliasB' => 'valueB',
            'aliasC' => 'valueC',
        );
        $option->setAliases($expected);

        $actual = $option->getAliases();
        $this->assertEquals($expected, $actual);
    }

    public function testSetAllowed()
    {
        $option = new Option('name', 'default');
        $allowed = array(
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueC',
        );
        $option->setAllowed($allowed);

        $actual = $option->getAllowed();
        $expected = array_values($allowed);
        $this->assertEquals($expected, $actual);
    }

    public function testAliases()
    {
        $option = new Option('name', 'default');
        $alias = 'aliasB';
        $expected = 'valueB';
        $aliases = array(
            'aliasA' => 'valueA',
            $alias   => $expected,
            'aliasC' => 'valueC',
        );
        $option->setAliases($aliases);

        $option->setDefault($alias);
        $actual = $option->getDefault();
        $this->assertEquals($expected, $actual);
    }
}
