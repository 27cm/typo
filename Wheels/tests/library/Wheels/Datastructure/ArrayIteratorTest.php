<?php

namespace Wheels\Datastructure;

use Wheels\Datastructure\ArrayIterator;

use PHPUnit_Framework_TestCase;

class ArrayIteratorTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $iterator = new ArrayIterator();

        $expected = array();
        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testGetArray()
    {
        $expected = array(1, 2, 3, 'a' => 'A');
        $iterator = new ArrayIterator($expected);

        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllowModifications()
    {
        $iterator = new ArrayIterator();

        $actual = $iterator->getAllowModifications();
        $this->assertTrue($actual);
    }

    public function testSetArray()
    {
        $iterator = new ArrayIterator();
        $expected = array(1, 2, 3, 'a' => 'A');
        $iterator->setArray($expected);

        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testSetAllowModifications()
    {
        $iterator = new ArrayIterator();
        $iterator->setAllowModifications(FALSE);

        $actual = $iterator->getAllowModifications();
        $this->assertFalse($actual);
    }

    public function testEnsureAllowModificationsException()
    {
        $this->setExpectedException(
            '\Wheels\Datastructure\Exception',
            'Изменение структуры данных запрещено'
        );

        $iterator = new ArrayIterator();
        $iterator->setAllowModifications(FALSE);

        $iterator->setArray(array(1, 2, 3));
    }

    public function testClear()
    {
        $iterator = new ArrayIterator(array(1, 2, 3));
        $iterator->clear();

        $expected = array();
        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testOffsetExists()
    {
        $expected = array(1, 2, 3, 'a' => 'A');
        $iterator = new ArrayIterator($expected);

        $actual = $iterator->offsetExists(2);
        $this->assertTrue($actual);

        $actual = $iterator->offsetExists(3);
        $this->assertFalse($actual);
    }

    public function testOffsetGet()
    {
        $expected = array(1, 2, 3, 'a' => 'A');
        $iterator = new ArrayIterator($expected);

        $expected = 2;
        $actual = $iterator->offsetGet(1);
        $this->assertEquals($expected, $actual);

        $expected = 'A';
        $actual = $iterator->offsetGet('a');
        $this->assertEquals($expected, $actual);
    }

    public function testOffsetSet()
    {
        $expected = array(1, 2);
        $iterator = new ArrayIterator($expected);

        $iterator->offsetSet(NULL, 3);
        $iterator->offsetSet('a', 'A');
        $iterator->offsetSet(0, 2);

        $expected[] = 3;
        $expected['a'] = 'A';
        $expected[0] = 2;

        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testOffsetUnset()
    {
        $expected = array(1, 2, 3, 'a' => 'A');
        $iterator = new ArrayIterator($expected);
        $iterator->offsetUnset('a');
        unset($expected['a']);

        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testCurrent()
    {
        $expected = 1;
        $iterator = new ArrayIterator(array($expected, 2, 3));

        $actual = $iterator->current();
        $this->assertEquals($expected, $actual);
    }

    public function testKey()
    {
        $expected = 'a';
        $iterator = new ArrayIterator(array($expected => 1, 2, 3));

        $actual = $iterator->key();
        $this->assertEquals($expected, $actual);
    }

    public function testRewind()
    {
        $expected = 1;
        $iterator = new ArrayIterator(array($expected, 2, 3));
        $iterator->next();
        $iterator->rewind();

        $actual = $iterator->current();
        $this->assertEquals($expected, $actual);
    }

    public function testNext()
    {
        $expected = 2;
        $iterator = new ArrayIterator(array(1, $expected, 3));
        $iterator->next();

        $actual = $iterator->current();
        $this->assertEquals($expected, $actual);
    }

    public function testValid()
    {
        $iterator = new ArrayIterator(array(1, 2, 3));
        $iterator->next();
        $iterator->next();

        $actual = $iterator->valid();
        $this->assertTrue($actual);

        $iterator->next();

        $actual = $iterator->valid();
        $this->assertFalse($actual);
    }

    public function testCount()
    {
        $iterator = new ArrayIterator(array(1, 2, 3));
        $iterator->offsetSet(NULL, 4);
        $iterator->offsetSet(NULL, 5);
        $iterator->offsetSet(NULL, 6);
        $iterator->offsetUnset(0);
        $iterator->offsetUnset(2);

        $expected = 4;
        $actual = $iterator->count();
        $this->assertEquals($expected, $actual);

        $iterator->clear();

        $expected = 0;
        $actual = $iterator->count();
        $this->assertEquals($expected, $actual);
    }

    public function testSerialize()
    {
        $array = array(1, 2, 3, 'a' => 'A');
        $iterator = new ArrayIterator($array);

        $expected = serialize($array);
        $actual = $iterator->serialize();
        $this->assertEquals($expected, $actual);
    }

    public function testUnserialize()
    {
        $expected = array(1, 2, 3, 'a' => 'A');
        $iterator = new ArrayIterator($expected);
        $serialized = $iterator->serialize();

        $iterator->unserialize($serialized);
        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }
}
