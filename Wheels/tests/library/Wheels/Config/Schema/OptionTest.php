<?php

namespace Wheels\Config\Schema;

use Wheels\Config\Schema\Option;
use Wheels\Config\Schema\Option\Type\Tmixed;
use Wheels\Config\Schema\Option\Type\Tstring;

use ReflectionClass;
use PHPUnit_Framework_TestCase;

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
        $type = new Tstring();
        $option = new Option('name', 'default', $type);

        $actual = $option->getType();
        $expected = get_class($type);
        $this->assertInstanceOf($expected, $actual);
    }

    public function testGetTypeDefault()
    {
        $type = new Tmixed();
        $option = new Option('name', 'default');

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

    public function testSetNameExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            'Имя параметра должно быть строкой'
        );

        $option = new Option('name', 'default');
        $option->setName(1);
    }

    public function testSetDefault()
    {
        $option = new Option('name', 'defaultA');
        $expected = 'defaultB';
        $option->setDefault($expected);

        $actual = $option->getDefault();
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultException()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            "Недопустимое значение по умолчанию параметра 'name'"
        );

        $option = new Option('name', 'default', 'string');
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
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            "Текстовое описание параметра 'name' должно быть строкой"
        );

        $option = new Option('name', 'default');
        $option->setDesc(1);
    }

    public function testSetAliases()
    {
        $option = new Option('name', 'default');
        $expected = array('aliasA' => 'valueA', 'aliasB' => 'valueB', 'aliasC' => 'valueC');
        $option->setAliases($expected);

        $actual = $option->getAliases();
        $this->assertEquals($expected, $actual);
    }

    public function testSetAliasesExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            "Недопустимое значение в массиве псевдонимов параметра 'name'"
        );

        $option = new Option('name', 'default', 'string');
        $aliases = array('aliasA' => 'valueA', 'aliasB' => 1, 'aliasC' => 'valueC');
        $option->setAliases($aliases);
    }

    public function testSetAliasesExceptionB()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            "Недопустимое значение в массиве псевдонимов параметра 'name'"
        );

        $option = new Option('name', 'valueA');
        $allowed = array('valueA', 'valueC');
        $option->setAllowed($allowed);
        $aliases = array('aliasA' => 'valueA', 'aliasB' => 'valueB', 'aliasC' => 'valueC');
        $option->setAliases($aliases);
    }

    public function testSetAllowed()
    {
        $option = new Option('name', 'valueA');
        $allowed = array('keyA' => 'valueA', 'valueB', 8 => 'valueC');
        $option->setAllowed($allowed);

        $actual = $option->getAllowed();
        $expected = array_values($allowed);
        $this->assertEquals($expected, $actual);
    }

    public function testSetAllowedExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            "Недопустимое значение в массиве допустимых значений параметра 'name'"
        );

        $option = new Option('name', 'default', 'string');
        $allowed = array('valueA', 'valueB', 1, 'valueC');
        $option->setAllowed($allowed);
    }

    public function testSetAllowedExceptionB()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            "Недопустимое значение в массиве псевдонимов параметра 'name'"
        );

        $option = new Option('name', 'default');
        $aliases = array('aliasA' => 'valueA', 'aliasB' => 'valueB', 'aliasC' => 'valueC');
        $option->setAliases($aliases);
        $allowed = array('valueA', 'valueC');
        $option->setAllowed($allowed);
    }

    public function testCreate()
    {
        $name    = 'name';
        $default = 'default';
        $type    = 'string';
        $desc    = 'Description...';
        $allowed = array('default', 'value');
        $aliases = array('alias' => 'value');

        $schema = array(
            'name'    => $name,
            'default' => $default,
            'type'    => $type,
            'desc'    => $desc,
            'allowed' => $allowed,
            'aliases' => $aliases,
        );
        $actual = Option::create($schema);

        $expected = new Option($name, $default, $type);
        $expected->setDesc($desc);
        $expected->setAllowed($allowed);
        $expected->setAliases($aliases);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            'Неизвестные разделы описания параметра: unknown'
        );

        $schema = array(
            'name'    => 'name',
            'default' => 'default',
            'unknown' => 1,
        );
        Option::create($schema);
    }

    public function testCreateExceptionB()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            'Не задано имя параметра'
        );

        $schema = array(
            'default' => 'default',
        );
        Option::create($schema);
    }

    public function testCreateExceptionC()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Option\Exception',
            'Не задано значение параметра по умолчанию'
        );

        $schema = array(
            'name' => 'name',
        );
        Option::create($schema);
    }

    public function testValidate()
    {
        $option = new Option('name', 'defaultA', 'string');
        $aliases = array('aliasA' => 'defaultA', 'aliasB' => 'defaultB', 'aliasC' => 'defaultC');
        $allowed = array('defaultA', 'defaultB', 'defaultC');
        $option->setAliases($aliases);
        $option->setAllowed($allowed);

        // Допустимое значение
        $actual = $option->validate('defaultB');
        $this->assertTrue($actual);

        // Допустимое значение (псевдоним)
        $actual = $option->validate('aliasB');
        $this->assertTrue($actual);

        // Неверный тип
        $actual = $option->validate(array('defaultB'));
        $this->assertFalse($actual);

        // Значение отсутствует в списке допустимых значений
        $actual = $option->validate('defaultD');
        $this->assertFalse($actual);
    }

    public function testFilter()
    {
        $option = new Option('name', 'default');

        $alias = 'aliasB';
        $expected = 'valueB';
        $aliases = array('aliasA' => 'valueA', $alias => $expected, 'aliasC' => 'valueC');
        $option->setAliases($aliases);

        $class = new ReflectionClass(get_class($option));
        $method = $class->getMethod('_filter');
        $method->setAccessible(true);

        // Псевдоним есть
        $actual = $method->invoke($option, $alias);
        $this->assertEquals($expected, $actual);

        // Псевдонима нет
        $expected = 'aliasD';
        $actual = $method->invoke($option, $expected);
        $this->assertEquals($expected, $actual);
    }
}
