<?php

namespace Wheels\Datastructure;

use Wheels\Datastructure\Collection;
use Wheels\Datastructure\Collection\A;
use Wheels\Datastructure\Collection\B;
use Wheels\Datastructure\Collection\A\C;

use PHPUnit_Framework_TestCase;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $a = new A();
        $elemsClass = get_class($a);

        new Collection($elemsClass);
    }

    public function testOffsetSet()
    {
        $a = new A();
        $c = new C();
        $elemsClass = get_class($a);

        $expected = array($a, 'c' => $c);
        $iterator = new Collection($elemsClass);

        $iterator->offsetSet(NULL, $a);
        $iterator->offsetSet('c', $c);

        $actual = $iterator->getArray();
        $this->assertEquals($expected, $actual);
    }

    public function testOffsetSetException()
    {
        $a = new A();
        $b = new B();
        $elemsClass = get_class($a);

        $this->setExpectedException(
            '\Wheels\Datastructure\Exception',
            "В коллекцию можно добавлять только объекты класса {$elemsClass}"
        );

        $iterator = new Collection($elemsClass);
        $iterator->offsetSet(NULL, $b);
    }
}
