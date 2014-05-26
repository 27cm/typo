<?php

namespace Tests\Wheels\Datastructure;

use Tests\TestCase;
use Tests\Wheels\Datastructure\Collection\A;
use Tests\Wheels\Datastructure\Collection\B;
use Tests\Wheels\Datastructure\Collection\A\C;

use Wheels\Datastructure\Collection;

class CollectionTest extends TestCase
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

        $iterator->offsetSet(null, $a);
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
        $iterator->offsetSet(null, $b);
    }
}
