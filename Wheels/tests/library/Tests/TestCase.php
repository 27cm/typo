<?php

namespace Tests;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    static public function getTestedClassname()
    {
        $classname = get_called_class();
        $classname = preg_replace('/^Tests\\\\|Test$/', '', $classname);

        return $classname;
    }

    static public function getDir($key)
    {
        $dirs = array(
            'data' => WHEELS_TESTS_LIBRARY_DIR . DS . 'Tests' . DS . static::getTestedClassname() . DS . '..' . DS . '_data',
        );

        return $dirs[$key];
    }
}
