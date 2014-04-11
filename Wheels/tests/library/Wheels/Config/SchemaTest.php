<?php

namespace Wheels\Config;

use Wheels\Config\Schema;
use Wheels\Config\Schema\Option;
use Wheels\Config\Schema\Option\Collection;

use PHPUnit_Framework_TestCase;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $schema = new Schema();

        $expected = new Collection();
        $actual = $schema->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testGetOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $schema = new Schema($options);

        $expected = new Collection($options);
        $actual = $schema->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testGetOption()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB');

        $options = array($a, $b);
        $schema = new Schema($options);

        $expected = $b;
        $actual = $schema->getOption('nameB');
        $this->assertEquals($expected, $actual);
    }

    public function testSetOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');

        $schema = new Schema();
        $options = array($a, $b);
        $schema->setOptions($options);

        $expected = new Collection($options);
        $actual = $schema->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testAddOptions()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $options = array($a);
        $schema = new Schema($options);

        $options = array($b, $c);
        $schema->addOptions($options);

        $options = array($a, $b, $c);
        $expected = new Collection($options);
        $actual = $schema->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testAddOption()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $options = array($a);
        $schema = new Schema($options);

        $schema->addOption($b);
        $schema->addOption($c);

        $options = array($a, $b, $c);
        $expected = new Collection($options);
        $actual = $schema->getOptions();
        $this->assertEquals($expected, $actual);
    }

    public function testCreate()
    {
        $a = new Option('nameA', 'defaultA');
        $b = new Option('nameB', 'defaultB', 'string');
        $c = new Option('nameC', 'defaultC');

        $modifications = TRUE;
        $case    = FALSE;
        $options = array(
            $a->getName() => array(
                'default' => $a->getDefault(),
            ),
            $b->getName() => array(
                'default' => $b->getDefault(),
                'type'    => 'string',
            ),
            $c->getName() => array(
                'default' => $c->getDefault(),
            ),
        );

        $schema = array(
            'allow-modifications' => $modifications,
            'case-sensitive'      => $case,
            'options'             => $options,
        );
        $actual = Schema::create($schema);

        $options = array($a, $b, $c);
        $expected = new Schema($options, $case);

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

    public function testCreateExceptionC()
    {
        $this->setExpectedException(
            '\Wheels\Config\Schema\Exception',
            'Описание параметра настроек должно быть массивом'
        );

        $schema = array(
            'options' => array(1),
        );
        Schema::create($schema);
    }
}
