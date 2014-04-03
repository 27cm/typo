<?php

namespace Wheels\Config;

use Wheels\Config\Schema;
use Wheels\Config\Schema\Option;

use PHPUnit_Framework_TestCase;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    public function testGetCaseSensitive()
    {
        $schema = new Schema();

        $actual = $schema->getCaseSensitive();
        $this->assertTrue($actual);
    }

    public function testGetOptions()
    {
        $schema = new Schema();

        $actual = $schema->getOptions();
        $expected = array();
        $this->assertEquals($expected, $actual);
    }

    public function testSetCaseSensitive()
    {
        $schema = new Schema();
        $schema->setCaseSensitive(FALSE);

        $actual = $schema->getCaseSensitive();
        $this->assertFalse($actual);
    }

    public function testSetOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $options = array(
            $a->getName() => array(
                'default' => $a->getDefault(),
            ),
            $b->getName() => array(
                'default' => $b->getDefault(),
            ),
        );

        $schema = new Schema();
        $schema->setOptions($options);

        $actual = $schema->getOptions();
        $expected = array(
            $a->getName() => $a,
            $b->getName() => $b,
        );
        $this->assertEquals($expected, $actual);
    }

    public function testAddOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $options = array(
            $a->getName() => array(
                'default' => $a->getDefault(),
            ),
        );

        $schema = new Schema();
        $schema->setOptions($options);

        $options = array(
            $b->getName() => array(
                'default' => $b->getDefault(),
            ),
            $c->getName() => array(
                'default' => $c->getDefault(),
            ),
        );
        $schema->addOptions($options);

        $actual = $schema->getOptions();
        $expected = array(
            $a->getName() => $a,
            $b->getName() => $b,
            $c->getName() => $c,
        );
        $this->assertEquals($expected, $actual);
    }

    public function testCreate()
    {
        $case    = FALSE;
        $options = array(
            'optionA' => array(
                'default' => 'defaultA',
            ),
            'optionB' => array(
                'default' => 'defaultB',
            ),
            'optionC' => array(
                'default' => 'defaultC',
            ),
        );

        $schema = array(
            'case-sensitive' => $case,
            'options'        => $options,
        );
        $actual = Schema::create($schema);

        $expected = new Schema();
        $expected->setCaseSensitive($case);
        $expected->setOptions($options);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateExceptionA()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Exception',
            'Неизвестные разделы описания конфигурации: unknown'
        );

        $schema = array(
            'unknown' => 1,
        );
        Schema::create($schema);
    }

    public function testCreateExceptionB()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Exception',
            "Раздел 'options' описания конфигурации должен быть массивом"
        );

        $schema = array(
            'options' => 1,
        );
        Schema::create($schema);
    }
}
