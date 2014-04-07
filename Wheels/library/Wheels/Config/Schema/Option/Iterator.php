<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 */

namespace Wheels\Config\Schema\Option;

//use Wheels\Config\Schema\Option;
//use Wheels\Config\Schema\Exception;

use ArrayIterator;

//class Collection extends ArrayObject
//{
//
//}
//
//class OptionsCollection extends Collection
//{
//
//}

/**
 * Класс обеспечивает доступ к объектам описаний параметров настроек как к массиву.
 */
class Iterator extends ArrayIterator
{
    /**
     * Описания параметров настроек.
     *
     * Ассоциативный массив с именами параметров в качестве ключей.
     *
     * @var \Wheels\Config\Schema\Option[]
     */
    private $storage = array();

    /**
     *
     * @return \Wheels\Config\Schema\Option
     */
    public function current()
    {
        return parent::current();
    }

    /**
     *
     * @return string
     */
    public function key()
    {
        return $this->current()->getName();
    }
}
